<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Helpers\PermissionHelper;
use App\Livewire\UsuarioCvListadoLivewire;
use App\Models\Usuario;
use App\Models\UsuarioCv;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UsuarioCvListadoLivewireTest extends TestCase
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
    public function lista_los_cvs_del_usuario(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create(['nombre' => 'CV Backend']);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvListadoLivewire::class, ['usuario' => $usuarioObjetivo])
            ->assertSee('CV Backend')
            ->assertViewHas('cvs', fn ($cvs) => $cvs->first()->is($cv));
    }

    #[Test]
    public function ordena_por_columna_aplicando_el_ciclo_asc_desc_fuera(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $antiguo = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create(['created_at' => now()->subDays(2)]);
        $reciente = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create(['created_at' => now()]);

        $componente = Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvListadoLivewire::class, ['usuario' => $usuarioObjetivo])
            ->assertSet('ordenacion', [])
            ->assertViewHas('cvs', fn ($cvs) => $cvs->first()->is($reciente));

        // Clic en "Creado" (sin ordenación activa) → se activa en asc (primer estado del ciclo)
        $componente->call('ordenarPorColumna', 'created_at')
            ->assertSet('ordenacion', ['created_at' => 'asc'])
            ->assertViewHas('cvs', fn ($cvs) => $cvs->first()->is($antiguo));

        // Clic de nuevo → pasa a desc
        $componente->call('ordenarPorColumna', 'created_at')
            ->assertSet('ordenacion', ['created_at' => 'desc'])
            ->assertViewHas('cvs', fn ($cvs) => $cvs->first()->is($reciente));

        // Clic de nuevo → se quita del todo
        $componente->call('ordenarPorColumna', 'created_at')
            ->assertSet('ordenacion', []);
    }

    #[Test]
    public function permite_multiordenacion_acumulando_columnas_sin_quitar_las_activas(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();

        $componente = Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvListadoLivewire::class, ['usuario' => $usuarioObjetivo])
            ->assertSet('ordenacion', []);

        $componente->call('ordenarPorColumna', 'created_at')
            ->assertSet('ordenacion', ['created_at' => 'asc']);

        // Activar "nombre" no quita "created_at": se acumulan (multiordenación, como en los CRUDs normales)
        $componente->call('ordenarPorColumna', 'nombre')
            ->assertSet('ordenacion', ['created_at' => 'asc', 'nombre' => 'asc']);

        $componente->call('ordenarPorColumna', 'updated_at')
            ->assertSet('ordenacion', ['created_at' => 'asc', 'nombre' => 'asc', 'updated_at' => 'asc']);
    }

    #[Test]
    public function ordena_por_nombre(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $b = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create(['nombre' => 'B CV']);
        $a = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create(['nombre' => 'A CV']);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvListadoLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('ordenarPorColumna', 'nombre')
            ->assertSet('ordenacion', ['nombre' => 'asc'])
            ->assertViewHas('cvs', fn ($cvs) => $cvs->first()->is($a));
    }

    #[Test]
    public function ignora_una_columna_de_ordenacion_no_permitida(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvListadoLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('ordenarPorColumna', 'email')
            ->assertSet('ordenacion', []);
    }

    #[Test]
    public function se_refresca_cuando_se_guarda_o_elimina_un_cv_desde_los_modales(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create(['nombre' => 'Nombre original']);

        $componente = Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvListadoLivewire::class, ['usuario' => $usuarioObjetivo])
            ->assertSee('Nombre original');

        // El modal de edición actualiza el nombre en BD; el listado, al escuchar 'cv-guardado', debe reflejarlo sin recargar la página
        $cv->update(['nombre' => 'Nombre editado']);
        $componente->dispatch('cv-guardado')
            ->assertSee('Nombre editado')
            ->assertDontSee('Nombre original');

        // El modal de borrado elimina el CV; el listado, al escuchar 'cv-eliminado', debe dejar de mostrarlo
        $cv->delete();
        $componente->dispatch('cv-eliminado')
            ->assertDontSee('Nombre editado');
    }
}
