<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

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
