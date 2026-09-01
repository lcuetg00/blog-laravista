<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Helpers\PermissionHelper;
use App\Models\Usuario;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UsuarioControllerTest extends TestCase
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
     * Crea un usuario con el permiso de listado (baseline del controller) más los permisos directos indicados, sin rol jerárquico.
     */
    private function usuarioConPermisos(array|string $permisos = []): Usuario
    {
        $usuario = Usuario::factory()->create();

        // El permiso de listado es el baseline del UsuarioController: se exige en todas sus acciones
        $usuario->givePermissionTo(array_unique(array_merge(
            [PermissionHelper::USUARIOS_LISTADO_PERMISSION],
            (array) $permisos,
        )));

        return $usuario;
    }

    /**
     * Crea un usuario con el rol indicado de RoleEnum (el rol admin hereda todos los permisos del sistema vía RoleSeeder).
     */
    private function usuarioConRol(RoleEnum $rol): Usuario
    {
        $usuario = Usuario::factory()->create();
        $usuario->assignRole(strtolower($rol->name));

        return $usuario;
    }

    #[Test]
    public function invitado_es_redirigido_al_login_en_el_listado(): void
    {
        $this->get(route('panel.usuarios.index'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function usuario_con_permiso_ve_el_listado(): void
    {
        $usuario = $this->usuarioConPermisos();

        $this->actingAs($usuario)
            ->get(route('panel.usuarios.index'))
            ->assertOk()
            ->assertViewIs('panel.usuarios.index')
            ->assertViewHas('usuarios');
    }

    #[Test]
    public function usuario_sin_permiso_no_accede_al_listado(): void
    {
        $usuario = Usuario::factory()->create();

        $this->actingAs($usuario)
            ->get(route('panel.usuarios.index'))
            ->assertForbidden();
    }

    #[Test]
    public function el_listado_filtra_por_email(): void
    {
        $usuario = $this->usuarioConPermisos();
        Usuario::factory()->create(['email' => 'buscado@ejemplo.test']);
        Usuario::factory()->create(['email' => 'oculto@ejemplo.test']);

        $this->actingAs($usuario)
            ->get(route('panel.usuarios.index', ['email' => 'buscado']))
            ->assertOk()
            ->assertSee('buscado@ejemplo.test')
            ->assertDontSee('oculto@ejemplo.test');
    }

    #[Test]
    public function usuario_con_permiso_ve_el_formulario_de_creacion(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CREAR_PERMISSION);

        $this->actingAs($usuario)
            ->get(route('panel.usuarios.create'))
            ->assertOk()
            ->assertViewIs('panel.usuarios.create');
    }

    #[Test]
    public function usuario_sin_permiso_no_ve_el_formulario_de_creacion(): void
    {
        $usuario = $this->usuarioConPermisos();

        $this->actingAs($usuario)
            ->get(route('panel.usuarios.create'))
            ->assertForbidden();
    }

    #[Test]
    public function crea_un_usuario_con_datos_validos_y_contrasena_hasheada(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CREAR_PERMISSION);

        $this->actingAs($usuario)
            ->post(route('panel.usuarios.store'), [
                'nombre' => 'Ana',
                'primer_apellido' => 'García',
                'segundo_apellido' => 'López',
                'email' => 'ana@ejemplo.test',
                'password' => 'Password1!',
                'password_confirmation' => 'Password1!',
            ])
            ->assertRedirect(route('panel.usuarios.index'))
            ->assertSessionHas('success');

        $nuevo = Usuario::where('email', 'ana@ejemplo.test')->first();
        $this->assertNotNull($nuevo);
        $this->assertNotNull($nuevo->ulid);
        // La contraseña debe quedar hasheada por el cast del modelo, nunca en texto plano
        $this->assertNotSame('Password1!', $nuevo->password);
        $this->assertTrue(Hash::check('Password1!', $nuevo->password));
    }

    #[Test]
    public function crea_un_usuario_sin_contrasena_generando_una_aleatoria(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CREAR_PERMISSION);

        $this->actingAs($usuario)
            ->post(route('panel.usuarios.store'), [
                'nombre' => 'Sin',
                'primer_apellido' => 'Clave',
                'email' => 'sinclave@ejemplo.test',
            ])
            ->assertRedirect(route('panel.usuarios.index'))
            ->assertSessionHas('success');

        $nuevo = Usuario::where('email', 'sinclave@ejemplo.test')->first();
        $this->assertNotNull($nuevo);
        $this->assertNotEmpty($nuevo->password);
    }

    #[Test]
    public function crea_un_usuario_con_una_imagen_valida_y_la_guarda_como_avatar(): void
    {
        Storage::fake('public');
        $usuario = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CREAR_PERMISSION);

        $this->actingAs($usuario)
            ->post(route('panel.usuarios.store'), [
                'nombre' => 'Con',
                'primer_apellido' => 'Imagen',
                'email' => 'conimagen@ejemplo.test',
                'imagen' => UploadedFile::fake()->image('avatar.jpg'),
            ])
            ->assertRedirect(route('panel.usuarios.index'))
            ->assertSessionHas('success');

        $nuevo = Usuario::where('email', 'conimagen@ejemplo.test')->first();
        $this->assertNotNull($nuevo->avatarUrl());
    }

    #[Test]
    public function no_crea_un_usuario_con_una_imagen_de_formato_no_permitido(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CREAR_PERMISSION);

        $this->actingAs($usuario)
            ->post(route('panel.usuarios.store'), [
                'nombre' => 'Sin',
                'primer_apellido' => 'Formato',
                'email' => 'malaimagen@ejemplo.test',
                'imagen' => UploadedFile::fake()->create('documento.pdf', 10, 'application/pdf'),
            ])
            ->assertSessionHasErrors('imagen');

        $this->assertDatabaseMissing('usuarios', ['email' => 'malaimagen@ejemplo.test']);
    }

    #[Test]
    public function no_crea_un_usuario_sin_email(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CREAR_PERMISSION);

        $this->actingAs($usuario)
            ->post(route('panel.usuarios.store'), [
                'nombre' => 'Ana',
                'primer_apellido' => 'García',
            ])
            ->assertSessionHasErrors('email');
    }

    #[Test]
    public function no_crea_un_usuario_con_email_invalido(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CREAR_PERMISSION);

        $this->actingAs($usuario)
            ->post(route('panel.usuarios.store'), [
                'nombre' => 'Ana',
                'primer_apellido' => 'García',
                'email' => 'no-es-un-email',
            ])
            ->assertSessionHasErrors('email');
    }

    #[Test]
    public function no_crea_un_usuario_con_email_duplicado(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CREAR_PERMISSION);
        Usuario::factory()->create(['email' => 'repetido@ejemplo.test']);

        $this->actingAs($usuario)
            ->post(route('panel.usuarios.store'), [
                'nombre' => 'Ana',
                'primer_apellido' => 'García',
                'email' => 'repetido@ejemplo.test',
            ])
            ->assertSessionHasErrors('email');
    }

    #[Test]
    public function no_crea_un_usuario_con_contrasena_sin_confirmar(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CREAR_PERMISSION);

        // La contraseña llega sin su confirmación, la regla 'confirmed' debe fallar
        $this->actingAs($usuario)
            ->post(route('panel.usuarios.store'), [
                'nombre' => 'Ana',
                'primer_apellido' => 'García',
                'email' => 'ana2@ejemplo.test',
                'password' => 'Password1!',
            ])
            ->assertSessionHasErrors('password');
    }

    #[Test]
    public function usuario_sin_permiso_no_puede_crear_un_usuario(): void
    {
        $usuario = $this->usuarioConPermisos();

        $this->actingAs($usuario)
            ->post(route('panel.usuarios.store'), [
                'nombre' => 'Ana',
                'primer_apellido' => 'García',
                'email' => 'noperm@ejemplo.test',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('usuarios', ['email' => 'noperm@ejemplo.test']);
    }

    #[Test]
    public function muestra_la_ficha_de_un_usuario(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::USUARIOS_VER_PERMISSION);
        $objetivo = Usuario::factory()->create(['email' => 'ficha@ejemplo.test']);

        $this->actingAs($usuario)
            ->get(route('panel.usuarios.show', $objetivo))
            ->assertOk()
            ->assertSee('ficha@ejemplo.test');
    }

    #[Test]
    public function un_admin_ve_el_formulario_de_edicion_de_un_usuario_gestionable(): void
    {
        $admin = $this->usuarioConRol(RoleEnum::ADMIN);
        $objetivo = $this->usuarioConRol(RoleEnum::USUARIO);

        $this->actingAs($admin)
            ->get(route('panel.usuarios.edit', $objetivo))
            ->assertOk()
            ->assertViewIs('panel.usuarios.edit');
    }

    #[Test]
    public function un_admin_actualiza_un_usuario_gestionable(): void
    {
        $admin = $this->usuarioConRol(RoleEnum::ADMIN);
        $objetivo = $this->usuarioConRol(RoleEnum::USUARIO);

        $this->actingAs($admin)
            ->put(route('panel.usuarios.update', $objetivo), [
                'nombre' => 'Nombre nuevo',
                'primer_apellido' => 'Apellido nuevo',
                'email' => 'actualizado@ejemplo.test',
            ])
            ->assertRedirect(route('panel.usuarios.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('usuarios', [
            'id' => $objetivo->id,
            'nombre' => 'Nombre nuevo',
            'email' => 'actualizado@ejemplo.test',
        ]);
    }

    #[Test]
    public function un_admin_actualiza_la_imagen_de_un_usuario_gestionable(): void
    {
        Storage::fake('public');
        $admin = $this->usuarioConRol(RoleEnum::ADMIN);
        $objetivo = $this->usuarioConRol(RoleEnum::USUARIO);

        $this->actingAs($admin)
            ->put(route('panel.usuarios.update', $objetivo), [
                'nombre' => $objetivo->nombre,
                'primer_apellido' => $objetivo->primer_apellido,
                'email' => $objetivo->email,
                'imagen' => UploadedFile::fake()->image('avatar.png'),
            ])
            ->assertRedirect(route('panel.usuarios.index'))
            ->assertSessionHas('success');

        $this->assertNotNull($objetivo->fresh()->avatarUrl());
    }

    #[Test]
    public function un_admin_no_puede_editarse_a_si_mismo(): void
    {
        // Regla de autoedición: nadie puede editarse salvo el superadmin
        $admin = $this->usuarioConRol(RoleEnum::ADMIN);

        $this->actingAs($admin)
            ->get(route('panel.usuarios.edit', $admin))
            ->assertForbidden();
    }

    #[Test]
    public function el_superadmin_si_puede_editarse_a_si_mismo(): void
    {
        $superadmin = $this->usuarioConRol(RoleEnum::SUPERADMIN);

        $this->actingAs($superadmin)
            ->get(route('panel.usuarios.edit', $superadmin))
            ->assertOk();
    }

    #[Test]
    public function un_admin_no_puede_editar_a_un_superadmin(): void
    {
        $admin = $this->usuarioConRol(RoleEnum::ADMIN);
        $superadmin = $this->usuarioConRol(RoleEnum::SUPERADMIN);

        $this->actingAs($admin)
            ->get(route('panel.usuarios.edit', $superadmin))
            ->assertForbidden();
    }

    #[Test]
    public function permite_conservar_el_mismo_email_al_actualizar(): void
    {
        $admin = $this->usuarioConRol(RoleEnum::ADMIN);
        $objetivo = $this->usuarioConRol(RoleEnum::USUARIO);

        // El unique del email debe ignorar al propio usuario editado
        $this->actingAs($admin)
            ->put(route('panel.usuarios.update', $objetivo), [
                'nombre' => $objetivo->nombre,
                'primer_apellido' => $objetivo->primer_apellido,
                'email' => $objetivo->email,
            ])
            ->assertSessionHasNoErrors();
    }

    #[Test]
    public function no_actualiza_con_el_email_de_otro_usuario(): void
    {
        $admin = $this->usuarioConRol(RoleEnum::ADMIN);
        $objetivo = $this->usuarioConRol(RoleEnum::USUARIO);
        $otro = Usuario::factory()->create(['email' => 'ocupado@ejemplo.test']);

        $this->actingAs($admin)
            ->put(route('panel.usuarios.update', $objetivo), [
                'nombre' => 'X',
                'primer_apellido' => 'Y',
                'email' => 'ocupado@ejemplo.test',
            ])
            ->assertSessionHasErrors('email');
    }

    #[Test]
    public function un_admin_elimina_un_usuario_gestionable_con_soft_delete(): void
    {
        $admin = $this->usuarioConRol(RoleEnum::ADMIN);
        $objetivo = $this->usuarioConRol(RoleEnum::USUARIO);

        $this->actingAs($admin)
            ->delete(route('panel.usuarios.destroy', $objetivo))
            ->assertRedirect(route('panel.usuarios.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('usuarios', ['id' => $objetivo->id]);
    }

    #[Test]
    public function nadie_puede_borrarse_a_si_mismo_ni_el_superadmin(): void
    {
        // Regla de autoeliminación: ni siquiera el superadmin puede borrarse
        $superadmin = $this->usuarioConRol(RoleEnum::SUPERADMIN);

        $this->actingAs($superadmin)
            ->delete(route('panel.usuarios.destroy', $superadmin))
            ->assertForbidden();

        $this->assertDatabaseHas('usuarios', ['id' => $superadmin->id, 'deleted_at' => null]);
    }

    #[Test]
    public function un_admin_no_puede_borrar_a_un_superadmin(): void
    {
        $admin = $this->usuarioConRol(RoleEnum::ADMIN);
        $superadmin = $this->usuarioConRol(RoleEnum::SUPERADMIN);

        $this->actingAs($admin)
            ->delete(route('panel.usuarios.destroy', $superadmin))
            ->assertForbidden();

        $this->assertDatabaseHas('usuarios', ['id' => $superadmin->id, 'deleted_at' => null]);
    }

    #[Test]
    public function usuario_sin_permiso_no_puede_eliminar(): void
    {
        $usuario = $this->usuarioConPermisos();
        $objetivo = Usuario::factory()->create();

        $this->actingAs($usuario)
            ->delete(route('panel.usuarios.destroy', $objetivo))
            ->assertForbidden();
    }

    #[Test]
    public function restaura_un_usuario_eliminado(): void
    {
        $usuario = $this->usuarioConPermisos(PermissionHelper::USUARIOS_RESTAURAR_PERMISSION);
        $objetivo = Usuario::factory()->create();
        $objetivo->delete();

        $this->actingAs($usuario)
            ->post(route('panel.usuarios.restore', $objetivo))
            ->assertRedirect(route('panel.usuarios.index'))
            ->assertSessionHas('success');

        $this->assertNotSoftDeleted('usuarios', ['id' => $objetivo->id]);
    }

    #[Test]
    public function usuario_sin_permiso_no_puede_restaurar(): void
    {
        $usuario = $this->usuarioConPermisos();
        $objetivo = Usuario::factory()->create();
        $objetivo->delete();

        $this->actingAs($usuario)
            ->post(route('panel.usuarios.restore', $objetivo))
            ->assertForbidden();
    }

    #[Test]
    public function exporta_el_listado_de_usuarios_a_excel(): void
    {
        Excel::fake();

        $usuario = $this->usuarioConPermisos(PermissionHelper::USUARIOS_EXPORTAR_PERMISSION);

        $nombreEsperado = trans('fields.usuarios.titulo') . ' - ' . now()->format('Y-m-d') . '.xlsx';

        $this->actingAs($usuario)
            ->get(route('panel.usuarios.export'))
            ->assertOk();

        Excel::assertDownloaded($nombreEsperado);
    }

    #[Test]
    public function usuario_sin_permiso_no_puede_exportar(): void
    {
        $usuario = $this->usuarioConPermisos();

        $this->actingAs($usuario)
            ->get(route('panel.usuarios.export'))
            ->assertForbidden();
    }
}
