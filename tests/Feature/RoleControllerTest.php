<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Helpers\PermissionHelper;
use App\Models\Role;
use App\Models\Usuario;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prepara permisos y roles del sistema y desactiva la redirección de idioma (en tests las rutas no llevan prefijo de locale).
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        // Sin un request real, mcamara registra las rutas sin prefijo de locale; desactivamos su redirección para no recibir 302
        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);
    }

    /**
     * Crea un usuario autenticable con el permiso de listado (baseline del controller) más los permisos indicados.
     */
    private function usuarioConPermisos(array|string $permisos = []): Usuario
    {
        $usuario = Usuario::factory()->create();

        // El permiso de listado es el baseline del RoleController: se exige en todas sus acciones
        $usuario->givePermissionTo(array_unique(array_merge(
            [PermissionHelper::ROLES_LISTADO_PERMISSION],
            (array) $permisos,
        )));

        return $usuario;
    }

    /**
     * Crea un usuario con el rol superadmin, que saltea cualquier comprobación de permisos
     */
    private function superadmin(): Usuario
    {
        $usuario = Usuario::factory()->create();
        $usuario->assignRole(strtolower(RoleEnum::SUPERADMIN->name));

        return $usuario;
    }

    /**
     * Crea un rol no protegido (id ajeno a RoleEnum) para los tests de edición y borrado
     */
    private function crearRol(array $atributos = []): Role
    {
        return Role::create(array_merge([
            'name' => 'editor',
            'guard_name' => 'web',
            'descripcion' => 'Rol de prueba',
        ], $atributos));
    }

    #[Test]
    public function invitado_es_redirigido_al_login_en_el_listado(): void
    {
        $this->get(route('panel.roles.index'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function usuario_con_permiso_ve_el_listado_de_roles(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_LISTADO_PERMISSION);

        $this->actingAs($usuario)
            ->get(route('panel.roles.index'))
            ->assertOk()
            ->assertViewIs('panel.roles.index')
            ->assertViewHas('roles');
    }

    #[Test]
    public function usuario_sin_permiso_no_accede_al_listado(): void
    {
        $usuario = Usuario::factory()->create();

        $this->actingAs($usuario)
            ->get(route('panel.roles.index'))
            ->assertForbidden();
    }

    #[Test]
    public function el_listado_filtra_por_busqueda_libre(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_LISTADO_PERMISSION);
        $this->crearRol(['name' => 'RolBuscado']);
        $this->crearRol(['name' => 'RolOculto']);

        $this->actingAs($usuario)
            ->get(route('panel.roles.index', ['busqueda' => 'Buscado']))
            ->assertOk()
            ->assertSee('RolBuscado')
            ->assertDontSee('RolOculto');
    }

    #[Test]
    public function usuario_con_permiso_ve_el_formulario_de_creacion(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_CREAR_PERMISSION);

        $this->actingAs($usuario)
            ->get(route('panel.roles.create'))
            ->assertOk()
            ->assertViewIs('panel.roles.create');
    }

    #[Test]
    public function usuario_sin_permiso_no_ve_el_formulario_de_creacion(): void
    {
        $usuario = Usuario::factory()->create();

        $this->actingAs($usuario)
            ->get(route('panel.roles.create'))
            ->assertForbidden();
    }

    #[Test]
    public function crea_un_rol_con_datos_validos(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_CREAR_PERMISSION);

        $this->actingAs($usuario)
            ->post(route('panel.roles.store'), [
                'name' => 'Editor de contenidos',
                'descripcion' => 'Puede editar contenidos',
            ])
            ->assertRedirect(route('panel.roles.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas(config('permission.table_names.roles'), [
            'name' => 'Editor de contenidos',
            'guard_name' => 'web',
            'descripcion' => 'Puede editar contenidos',
        ]);

        // El trait HasPublicUlid debe haber autorrellenado el ulid público
        $rol = Role::where('name', 'Editor de contenidos')->first();
        $this->assertNotNull($rol->ulid);
    }

    #[Test]
    public function no_crea_un_rol_sin_nombre(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_CREAR_PERMISSION);

        $this->actingAs($usuario)
            ->post(route('panel.roles.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function no_crea_un_rol_con_nombre_duplicado(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_CREAR_PERMISSION);
        $this->crearRol(['name' => 'Repetido']);

        $this->actingAs($usuario)
            ->post(route('panel.roles.store'), ['name' => 'Repetido'])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function no_crea_un_rol_con_caracteres_no_permitidos(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_CREAR_PERMISSION);

        // El doble guion está prohibido por REGEX_TEXTO (defensa frente a comentarios SQL)
        $this->actingAs($usuario)
            ->post(route('panel.roles.store'), ['name' => 'rol--malicioso'])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function usuario_sin_permiso_no_puede_crear_un_rol(): void
    {
        $usuario = Usuario::factory()->create();

        $this->actingAs($usuario)
            ->post(route('panel.roles.store'), ['name' => 'No permitido'])
            ->assertForbidden();

        $this->assertDatabaseMissing(config('permission.table_names.roles'), [
            'name' => 'No permitido',
        ]);
    }

    #[Test]
    public function muestra_la_ficha_de_un_rol(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_VER_PERMISSION);
        $rol = $this->crearRol(['name' => 'RolDetalle']);

        $this->actingAs($usuario)
            ->get(route('panel.roles.show', $rol))
            ->assertOk()
            ->assertSee('RolDetalle');
    }

    #[Test]
    public function muestra_el_formulario_de_edicion_de_un_rol(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_EDITAR_PERMISSION);
        $rol = $this->crearRol();

        $this->actingAs($usuario)
            ->get(route('panel.roles.edit', $rol))
            ->assertOk()
            ->assertViewIs('panel.roles.edit');
    }

    #[Test]
    public function actualiza_un_rol_existente(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_EDITAR_PERMISSION);
        $rol = $this->crearRol(['name' => 'Antiguo']);

        $this->actingAs($usuario)
            ->put(route('panel.roles.update', $rol), [
                'name' => 'Nuevo nombre',
                'descripcion' => 'Descripción actualizada',
            ])
            ->assertRedirect(route('panel.roles.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas(config('permission.table_names.roles'), [
            'id' => $rol->id,
            'name' => 'Nuevo nombre',
            'descripcion' => 'Descripción actualizada',
        ]);
    }

    #[Test]
    public function permite_conservar_el_mismo_nombre_al_actualizar(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_EDITAR_PERMISSION);
        $rol = $this->crearRol(['name' => 'Sin cambios']);

        // El unique debe ignorar al propio rol editado
        $this->actingAs($usuario)
            ->put(route('panel.roles.update', $rol), ['name' => 'Sin cambios'])
            ->assertSessionHasNoErrors();
    }

    #[Test]
    public function no_actualiza_con_el_nombre_de_otro_rol(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_EDITAR_PERMISSION);
        $this->crearRol(['name' => 'Ocupado']);
        $rol = $this->crearRol(['name' => 'Editable']);

        $this->actingAs($usuario)
            ->put(route('panel.roles.update', $rol), ['name' => 'Ocupado'])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function elimina_un_rol_no_protegido(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_ELIMINAR_PERMISSION);
        $rol = $this->crearRol();

        $this->actingAs($usuario)
            ->delete(route('panel.roles.destroy', $rol))
            ->assertRedirect(route('panel.roles.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing(config('permission.table_names.roles'), [
            'id' => $rol->id,
        ]);
    }

    #[Test]
    public function no_elimina_un_rol_protegido_del_sistema(): void
    {
        // El superadmin saltea los permisos, pero el rol protegido debe seguir bloqueado por RoleHelper
        $usuario = $this->superadmin();
        $rolProtegido = Role::findById(RoleEnum::ADMIN->value);

        $this->actingAs($usuario)
            ->delete(route('panel.roles.destroy', $rolProtegido))
            ->assertForbidden();

        $this->assertDatabaseHas(config('permission.table_names.roles'), [
            'id' => RoleEnum::ADMIN->value,
        ]);
    }

    #[Test]
    public function usuario_sin_permiso_no_puede_eliminar_un_rol(): void
    {
        $usuario = Usuario::factory()->create();
        $rol = $this->crearRol();

        $this->actingAs($usuario)
            ->delete(route('panel.roles.destroy', $rol))
            ->assertForbidden();

        $this->assertDatabaseHas(config('permission.table_names.roles'), [
            'id' => $rol->id,
        ]);
    }

    #[Test]
    public function exporta_el_listado_de_roles_a_excel(): void
    {
        Excel::fake();

        $usuario = $this->usuarioConPermisos(PermissionHelper::ROLES_EXPORTAR_PERMISSION);

        $nombreEsperado = trans('fields.roles.titulo') . ' - ' . now()->format('Y-m-d') . '.xlsx';

        $this->actingAs($usuario)
            ->get(route('panel.roles.export'))
            ->assertOk();

        Excel::assertDownloaded($nombreEsperado);
    }

    #[Test]
    public function usuario_sin_permiso_no_puede_exportar(): void
    {
        $usuario = Usuario::factory()->create();

        $this->actingAs($usuario)
            ->get(route('panel.roles.export'))
            ->assertForbidden();
    }
}
