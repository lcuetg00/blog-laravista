<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Helpers\PermissionHelper;
use App\Livewire\UsuarioCvDuplicarModalLivewire;
use App\Models\Usuario;
use App\Models\UsuarioCv;
use App\Models\UsuarioCvSeccion;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UsuarioCvDuplicarModalLivewireTest extends TestCase
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
    public function abrir_el_modal_precarga_un_nombre_sugerido_a_partir_del_original(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create(['nombre' => 'CV original']);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvDuplicarModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', $cv->ulid)
            ->assertSet('cvUlid', $cv->ulid)
            ->assertSet('nombre', trans('fields.usuarios_cvs.modal.duplicar_nombre_sugerido', ['nombre' => 'CV original']));
    }

    #[Test]
    public function duplica_el_cv_con_sus_secciones_y_las_imagenes_de_la_galeria(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cvOriginal = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create([
            'nombre' => 'CV original',
            'nombre_archivo' => 'archivo original',
            'color_primario' => '#123456',
        ]);
        $seccion = UsuarioCvSeccion::factory()->for($cvOriginal, 'usuarioCv')->create([
            'orden' => 0,
            'titulo' => 'Experiencia',
            'sangria' => true,
        ]);
        $seccion->addMedia(UploadedFile::fake()->image('foto.jpg'))
            ->toMediaCollection(UsuarioCvSeccion::MEDIA_COLLECTION_GALLERY);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvDuplicarModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', $cvOriginal->ulid)
            ->set('nombre', 'CV duplicado')
            ->call('duplicar')
            ->assertHasNoErrors()
            ->assertDispatched('cv-guardado')
            ->assertDispatched('duplicar-cv-cerrar');

        $copia = UsuarioCv::where('usuario_id', $usuarioObjetivo->id)->where('nombre', 'CV duplicado')->first();
        $this->assertNotNull($copia);
        self::assertNotSame($cvOriginal->id, $copia->id);
        self::assertNotSame($cvOriginal->ulid, $copia->ulid);
        self::assertSame('CV duplicado', $copia->nombre_archivo);
        self::assertSame('#123456', $copia->color_primario);

        self::assertSame(1, $copia->secciones()->count());
        $seccionCopiada = $copia->secciones()->first();
        self::assertSame('Experiencia', $seccionCopiada->titulo);
        self::assertTrue($seccionCopiada->sangria);
        self::assertNotSame($seccion->ulid, $seccionCopiada->ulid);
        self::assertSame(1, $seccionCopiada->getMedia(UsuarioCvSeccion::MEDIA_COLLECTION_GALLERY)->count());

        // El CV y la sección originales no se han tocado
        self::assertSame('CV original', $cvOriginal->fresh()->nombre);
        self::assertSame(1, $seccion->fresh()->getMedia(UsuarioCvSeccion::MEDIA_COLLECTION_GALLERY)->count());
    }

    #[Test]
    public function no_duplica_sin_nombre(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvDuplicarModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', $cv->ulid)
            ->set('nombre', '')
            ->call('duplicar')
            ->assertHasErrors(['nombre' => 'required']);

        self::assertSame(1, UsuarioCv::where('usuario_id', $usuarioObjetivo->id)->count());
    }

    #[Test]
    public function sin_permiso_de_crear_no_puede_duplicar_un_cv(): void
    {
        $usuarioActivo = $this->usuarioConPermisos();
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvDuplicarModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrirFormulario', $cv->ulid)
            ->set('nombre', 'CV cualquiera')
            ->call('duplicar')
            ->assertForbidden();

        self::assertSame(1, UsuarioCv::where('usuario_id', $usuarioObjetivo->id)->count());
    }
}
