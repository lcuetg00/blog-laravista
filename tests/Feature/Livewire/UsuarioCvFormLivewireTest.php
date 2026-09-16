<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\FontSizeEnum;
use App\Helpers\PermissionHelper;
use App\Livewire\UsuarioCvFormLivewire;
use App\Models\Usuario;
use App\Models\UsuarioCv;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UsuarioCvFormLivewireTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    private function usuarioConPermisos(array|string $permisos = []): Usuario
    {
        $usuario = Usuario::factory()->create();
        $usuario->givePermissionTo((array) $permisos);

        return $usuario;
    }

    #[Test]
    public function crea_un_cv_nuevo_y_emite_el_evento_de_guardado(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', null)
            ->set('nombre', 'CV Frontend 2026')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertDispatched('cv-guardado');

        self::assertSame(1, UsuarioCv::where('usuario_id', $usuarioObjetivo->id)->where('nombre', 'CV Frontend 2026')->count());
    }

    #[Test]
    public function crea_un_cv_con_tamanos_de_fuente_personalizados(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', null)
            ->set('nombre', 'CV con tamaños')
            ->set('fontSizeCabecera', FontSizeEnum::LARGE)
            ->set('fontSizeContenido', FontSizeEnum::SMALL)
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertDispatched('cv-guardado');

        $cv = UsuarioCv::where('usuario_id', $usuarioObjetivo->id)->where('nombre', 'CV con tamaños')->first();
        $this->assertNotNull($cv);
        $this->assertSame(FontSizeEnum::LARGE, $cv->font_size_cabecera);
        $this->assertSame(FontSizeEnum::SMALL, $cv->font_size_contenido);
    }

    #[Test]
    public function precarga_los_tamanos_de_fuente_de_un_cv_existente(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create([
            'font_size_cabecera' => FontSizeEnum::SMALL,
            'font_size_contenido' => FontSizeEnum::LARGE,
        ]);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', $cv->ulid)
            ->assertSet('fontSizeCabecera', FontSizeEnum::SMALL)
            ->assertSet('fontSizeContenido', FontSizeEnum::LARGE);
    }

    #[Test]
    public function al_editar_un_cv_existente_emite_el_evento_de_vista_previa_con_su_url(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create(['nombre' => 'CV a previsualizar']);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', $cv->ulid)
            ->assertDispatched('recargar-preview-cv-form', function (string $nombre, array $params) use ($usuarioObjetivo, $cv): bool {
                return $params['titulo'] === 'CV a previsualizar'
                    && $params['url'] === route('panel.usuarios.cvs.pdf', [$usuarioObjetivo, $cv]);
            });
    }

    #[Test]
    public function al_crear_un_cv_nuevo_emite_el_evento_de_vista_previa_sin_url(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', null)
            ->assertDispatched('recargar-preview-cv-form', function (string $nombre, array $params): bool {
                return $params['url'] === null;
            });
    }

    #[Test]
    public function no_crea_un_cv_sin_nombre(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', null)
            ->set('nombre', '')
            ->call('guardar')
            ->assertHasErrors(['nombre' => 'required']);

        self::assertSame(0, UsuarioCv::where('usuario_id', $usuarioObjetivo->id)->count());
    }

    #[Test]
    public function precarga_y_edita_el_nombre_de_un_cv_existente(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create(['nombre' => 'Nombre original']);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', $cv->ulid)
            ->assertSet('nombre', 'Nombre original')
            ->set('nombre', 'Nombre editado')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertDispatched('cv-guardado');

        self::assertSame('Nombre editado', $cv->fresh()->nombre);
    }

    #[Test]
    public function guarda_el_nombre_del_archivo_cuando_se_informa(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', null)
            ->set('nombre', 'CV con nombre de archivo')
            ->set('nombreArchivo', 'nombre para el pdf')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertDispatched('cv-guardado');

        $cv = UsuarioCv::where('usuario_id', $usuarioObjetivo->id)->where('nombre', 'CV con nombre de archivo')->first();
        $this->assertNotNull($cv);
        self::assertSame('nombre para el pdf', $cv->nombre_archivo);
    }

    #[Test]
    public function el_nombre_del_archivo_queda_en_null_si_se_deja_en_blanco(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', null)
            ->set('nombre', 'CV sin nombre de archivo')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertDispatched('cv-guardado');

        $cv = UsuarioCv::where('usuario_id', $usuarioObjetivo->id)->where('nombre', 'CV sin nombre de archivo')->first();
        $this->assertNotNull($cv);
        self::assertNull($cv->nombre_archivo);
    }

    #[Test]
    public function precarga_el_nombre_del_archivo_de_un_cv_existente(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create(['nombre_archivo' => 'archivo original']);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', $cv->ulid)
            ->assertSet('nombreArchivo', 'archivo original');
    }

    #[Test]
    public function sin_permiso_de_crear_no_puede_crear_un_cv(): void
    {
        $usuarioActivo = $this->usuarioConPermisos();
        $usuarioObjetivo = Usuario::factory()->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', null)
            ->set('nombre', 'CV cualquiera')
            ->call('guardar')
            ->assertForbidden();
    }

    #[Test]
    public function abre_el_modal_de_eliminar_y_muestra_el_nombre_del_cv(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_ELIMINAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create(['nombre' => 'CV a borrar']);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirEliminar', $cv->ulid)
            ->assertSet('cvUlidEliminar', $cv->ulid)
            ->assertSee('CV a borrar');
    }

    #[Test]
    public function elimina_el_cv_confirmado_y_emite_el_evento(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_ELIMINAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirEliminar', $cv->ulid)
            ->call('eliminarCv')
            ->assertDispatched('cv-eliminado');

        self::assertTrue($cv->fresh()->trashed());
    }

    #[Test]
    public function sin_permiso_de_eliminar_no_puede_eliminar_un_cv(): void
    {
        $usuarioActivo = $this->usuarioConPermisos();
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirEliminar', $cv->ulid)
            ->call('eliminarCv')
            ->assertForbidden();

        self::assertFalse($cv->fresh()->trashed());
    }

    #[Test]
    public function pide_confirmar_descarte_al_intentar_cerrar_el_modal_con_cambios_sin_guardar(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', null)
            ->set('nombre', 'CV sin guardar')
            ->call('intentarCerrarModal')
            ->assertNotDispatched('cv-cerrar')
            ->assertDispatched('cv-abrir-confirmar-descarte')
            ->call('confirmarDescarte')
            ->assertDispatched('cv-cerrar')
            ->assertSet('hayCambiosSinGuardar', false);
    }

    #[Test]
    public function cierra_el_modal_directamente_si_no_hay_cambios_sin_guardar(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvFormLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', null)
            ->call('intentarCerrarModal')
            ->assertDispatched('cv-cerrar')
            ->assertNotDispatched('cv-abrir-confirmar-descarte');
    }
}
