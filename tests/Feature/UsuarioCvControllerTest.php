<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Helpers\PermissionHelper;
use App\Models\Usuario;
use App\Models\UsuarioCv;
use App\Models\UsuarioCvSeccion;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    #[Test]
    public function invitado_no_puede_generar_pdf(): void
    {
        $usuario = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuario, 'usuario')->create();

        $this->get(route('panel.usuarios.cvs.pdf', [$usuario, $cv]))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function usuario_sin_permiso_no_puede_generar_el_pdf(): void
    {
        $usuario = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuario, 'usuario')->create();
        $usuarioActivo = Usuario::factory()->create();

        $this->actingAs($usuarioActivo)
            ->get(route('panel.usuarios.cvs.pdf', [$usuario, $cv]))
            ->assertForbidden();
    }

    #[Test]
    public function devuelve_404_si_el_cv_no_pertenece_al_usuario_de_la_url(): void
    {
        $usuario = Usuario::factory()->create();
        $otroUsuario = Usuario::factory()->create();
        $cvDeOtroUsuario = UsuarioCv::factory()->for($otroUsuario, 'usuario')->create();

        $usuarioActivo = Usuario::factory()->create();
        $usuarioActivo->givePermissionTo([
            PermissionHelper::USUARIOS_LISTADO_PERMISSION,
            PermissionHelper::USUARIOS_CVS_GENERAR_PDF_PERMISSION,
        ]);

        $this->actingAs($usuarioActivo)
            ->get(route('panel.usuarios.cvs.pdf', [$usuario, $cvDeOtroUsuario]))
            ->assertNotFound();
    }

    #[Test]
    public function usuario_con_permiso_genera_el_pdf_del_cv(): void
    {
        $usuario = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuario, 'usuario')->create();
        UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 0, 'titulo' => 'Experiencia']);

        $usuarioActivo = Usuario::factory()->create();
        $usuarioActivo->givePermissionTo([
            PermissionHelper::USUARIOS_LISTADO_PERMISSION,
            PermissionHelper::USUARIOS_CVS_GENERAR_PDF_PERMISSION,
        ]);

        $response = $this->actingAs($usuarioActivo)
            ->get(route('panel.usuarios.cvs.pdf', [$usuario, $cv]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        // Comprobación de "es un PDF real" sin parsear el binario: la cabecera %PDF- es constante en cualquier PDF válido
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    #[Test]
    public function usuario_con_permiso_genera_el_pdf_del_cv_con_imagenes_en_la_galeria(): void
    {
        $usuario = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuario, 'usuario')->create();
        $seccion = UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 0, 'titulo' => 'Experiencia']);
        $seccion->addMedia(UploadedFile::fake()->image('foto.jpg'))
            ->toMediaCollection(UsuarioCvSeccion::MEDIA_COLLECTION_GALLERY);

        $usuarioActivo = Usuario::factory()->create();
        $usuarioActivo->givePermissionTo([
            PermissionHelper::USUARIOS_LISTADO_PERMISSION,
            PermissionHelper::USUARIOS_CVS_GENERAR_PDF_PERMISSION,
        ]);

        $response = $this->actingAs($usuarioActivo)
            ->get(route('panel.usuarios.cvs.pdf', [$usuario, $cv]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    #[Test]
    public function el_pdf_usa_el_nombre_del_archivo_cuando_esta_informado(): void
    {
        $usuario = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuario, 'usuario')->create([
            'nombre' => 'CV visible',
            'nombre_archivo' => 'nombre para el pdf',
        ]);

        $usuarioActivo = Usuario::factory()->create();
        $usuarioActivo->givePermissionTo([
            PermissionHelper::USUARIOS_LISTADO_PERMISSION,
            PermissionHelper::USUARIOS_CVS_GENERAR_PDF_PERMISSION,
        ]);

        $response = $this->actingAs($usuarioActivo)
            ->get(route('panel.usuarios.cvs.pdf', [$usuario, $cv]))
            ->assertOk();

        $this->assertStringContainsString('nombre-para-el-pdf', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringNotContainsString('cv-visible', (string) $response->headers->get('Content-Disposition'));
    }

    #[Test]
    public function el_pdf_usa_el_nombre_del_cv_si_no_hay_nombre_de_archivo(): void
    {
        $usuario = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuario, 'usuario')->create([
            'nombre' => 'CV visible',
            'nombre_archivo' => null,
        ]);

        $usuarioActivo = Usuario::factory()->create();
        $usuarioActivo->givePermissionTo([
            PermissionHelper::USUARIOS_LISTADO_PERMISSION,
            PermissionHelper::USUARIOS_CVS_GENERAR_PDF_PERMISSION,
        ]);

        $response = $this->actingAs($usuarioActivo)
            ->get(route('panel.usuarios.cvs.pdf', [$usuario, $cv]))
            ->assertOk();

        $this->assertStringContainsString('cv-visible', (string) $response->headers->get('Content-Disposition'));
    }
}
