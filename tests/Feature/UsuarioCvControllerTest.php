<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Helpers\PermissionHelper;
use App\Models\Usuario;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UsuarioCvControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prepara permisos del sistema y desactiva la redirección de idioma (en tests las rutas no llevan prefijo de locale).
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);
    }

    #[Test]
    public function invitado_es_redirigido_al_login(): void
    {
        $usuario = Usuario::factory()->create();

        $this->get(route('panel.usuarios.cvs', $usuario))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function usuario_sin_permiso_recibe_403(): void
    {
        $usuarioObjetivo = Usuario::factory()->create();
        $usuarioActivo = Usuario::factory()->create();

        $this->actingAs($usuarioActivo)
            ->get(route('panel.usuarios.cvs', $usuarioObjetivo))
            ->assertForbidden();
    }

    #[Test]
    public function usuario_con_permiso_ve_la_pantalla(): void
    {
        $usuarioObjetivo = Usuario::factory()->create();
        $usuarioActivo = Usuario::factory()->create();
        // USUARIOS_LISTADO_PERMISSION es el permiso base que UsuarioController exige en todas sus acciones (middleware de clase)
        $usuarioActivo->givePermissionTo([
            PermissionHelper::USUARIOS_LISTADO_PERMISSION,
            PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION,
        ]);

        $this->actingAs($usuarioActivo)
            ->get(route('panel.usuarios.cvs', $usuarioObjetivo))
            ->assertOk()
            ->assertViewIs('panel.usuarios.listado-cvs')
            ->assertViewHas('usuario', fn (Usuario $u): bool => $u->is($usuarioObjetivo));
    }
}
