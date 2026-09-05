<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Helpers\PermissionHelper;
use App\Livewire\UsuarioCvSeccionesModalLivewire;
use App\Models\Usuario;
use App\Models\UsuarioCv;
use App\Models\UsuarioCvSeccion;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UsuarioCvSeccionesModalLivewireTest extends TestCase
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

    private function crearSeccion(): UsuarioCvSeccion
    {
        $usuario = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuario, 'usuario')->create();

        return UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 0]);
    }

    #[Test]
    public function abre_el_modal_y_carga_las_secciones_del_cv(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();
        UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 0]);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->assertSet('cvUlid', $cv->ulid);
    }

    #[Test]
    public function refleja_el_titulo_en_vivo_en_la_fila_sin_guardarlo_en_bd(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();
        $seccion = UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['titulo' => 'Título original', 'orden' => 0]);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->set('titulo', 'Título en vivo')
            ->assertSee('Título en vivo')
            ->assertSet('hayCambiosSinGuardar', true);

        $this->assertSame('Título original', $seccion->fresh()->titulo);
    }

    #[Test]
    public function crea_una_seccion_vacia_al_final_y_avisa_al_listado(): void
    {
        $usuarioActivo = $this->usuarioConPermisos([
            PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION,
            PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION,
        ]);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();
        UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 0]);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->call('crearSeccionVacia')
            ->assertDispatched('secciones-actualizadas');

        self::assertSame(2, $cv->secciones()->count());
        self::assertSame(1, (int) $cv->secciones()->max('orden'));
    }

    #[Test]
    public function reordena_las_secciones_sin_duplicar_el_orden(): void
    {
        $usuarioActivo = $this->usuarioConPermisos([
            PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION,
            PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION,
        ]);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();

        $primera = UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 0]);
        $segunda = UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 1]);
        $tercera = UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 2]);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->call('reordenarSecciones', [$tercera->ulid, $primera->ulid, $segunda->ulid]);

        $ordenFinal = $cv->secciones()->pluck('ulid')->values()->all();
        self::assertSame([$tercera->ulid, $primera->ulid, $segunda->ulid], $ordenFinal);

        $ordenes = $cv->secciones()->pluck('orden')->all();
        self::assertSame($ordenes, array_unique($ordenes));
    }

    #[Test]
    public function ignora_el_reordenado_si_el_array_no_contiene_exactamente_las_secciones_del_cv(): void
    {
        $usuarioActivo = $this->usuarioConPermisos([
            PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION,
            PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION,
        ]);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();

        $primera = UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 0]);
        $segunda = UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 1]);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->call('reordenarSecciones', [$segunda->ulid, 'ulid-inventado']);

        self::assertSame(0, $primera->fresh()->orden);
        self::assertSame(1, $segunda->fresh()->orden);
    }

    #[Test]
    public function al_abrir_selecciona_automaticamente_la_primera_seccion_por_orden(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();
        $primera = UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 0]);
        $segunda = UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 1]);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->assertSet('seccionSeleccionadaUlid', $primera->ulid)
            ->assertSet('titulo', $primera->titulo)
            ->call('seleccionarSeccion', $segunda->ulid)
            ->assertSet('seccionSeleccionadaUlid', $segunda->ulid);
    }

    #[Test]
    public function al_abrir_sin_secciones_no_selecciona_ninguna(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->assertSet('seccionSeleccionadaUlid', null);
    }

    #[Test]
    public function limpia_la_seleccion_si_la_seccion_seleccionada_se_elimina(): void
    {
        $usuarioActivo = $this->usuarioConPermisos([
            PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION,
            PermissionHelper::USUARIOS_CVS_ELIMINAR_PERMISSION,
        ]);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();
        $seccion = UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 0]);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->call('seleccionarSeccion', $seccion->ulid)
            ->assertSet('seccionSeleccionadaUlid', $seccion->ulid)
            ->call('eliminar')
            ->assertDispatched('secciones-actualizadas')
            ->assertSet('seccionSeleccionadaUlid', null);

        self::assertTrue($seccion->fresh()->trashed());
    }

    #[Test]
    public function sin_permiso_de_crear_no_puede_anadir_una_seccion(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->call('crearSeccionVacia')
            ->assertForbidden();
    }

    #[Test]
    public function guarda_titulo_y_descripcion_admitiendo_html(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION);
        $seccion = $this->crearSeccion();
        $cv = $seccion->usuarioCv;

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $cv->usuario])
            ->call('abrir', $cv->ulid)
            ->set('titulo', 'Experiencia laboral')
            ->set('descripcion', '<b>Desarrollador</b> desde 2020')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertDispatched('secciones-actualizadas')
            ->assertSet('hayCambiosSinGuardar', false);

        $seccion->refresh();
        self::assertSame('Experiencia laboral', $seccion->titulo);
        self::assertSame('<b>Desarrollador</b> desde 2020', $seccion->descripcion);
    }

    #[Test]
    public function sanea_el_html_peligroso_de_la_descripcion_al_guardar(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION);
        $seccion = $this->crearSeccion();
        $cv = $seccion->usuarioCv;

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $cv->usuario])
            ->call('abrir', $cv->ulid)
            ->set('titulo', 'Experiencia laboral')
            ->set('descripcion', '<script>alert(1)</script><p onclick="alert(2)">Hola</p>')
            ->call('guardar')
            ->assertHasNoErrors();

        $seccion->refresh();
        self::assertStringNotContainsString('<script', $seccion->descripcion);
        self::assertStringNotContainsString('onclick', $seccion->descripcion);
        self::assertStringContainsString('Hola', $seccion->descripcion);
    }

    #[Test]
    public function conserva_el_atributo_style_de_la_descripcion_al_guardar(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION);
        $seccion = $this->crearSeccion();
        $cv = $seccion->usuarioCv;

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $cv->usuario])
            ->call('abrir', $cv->ulid)
            ->set('titulo', 'Experiencia laboral')
            ->set('descripcion', '<p style="color: red; font-weight: bold;">Hola</p>')
            ->call('guardar')
            ->assertHasNoErrors();

        $seccion->refresh();
        self::assertStringContainsString('style="color: red; font-weight: bold;"', $seccion->descripcion);
    }

    #[Test]
    public function no_guarda_sin_titulo(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION);
        $seccion = $this->crearSeccion();
        $cv = $seccion->usuarioCv;

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $cv->usuario])
            ->call('abrir', $cv->ulid)
            ->set('titulo', '')
            ->call('guardar')
            ->assertHasErrors(['titulo' => 'required']);
    }

    #[Test]
    public function sube_y_borra_imagenes_de_la_galeria(): void
    {
        Storage::fake('public');

        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION);
        $seccion = $this->crearSeccion();
        $cv = $seccion->usuarioCv;

        $componente = Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $cv->usuario])
            ->call('abrir', $cv->ulid)
            ->set('galeriaNuevas', [
                UploadedFile::fake()->image('foto1.jpg'),
                UploadedFile::fake()->image('foto2.jpg'),
            ])
            ->call('guardar')
            ->assertHasNoErrors();

        $seccion->refresh();
        self::assertCount(2, $seccion->getMedia(UsuarioCvSeccion::MEDIA_COLLECTION_GALLERY));

        $media = $seccion->getMedia(UsuarioCvSeccion::MEDIA_COLLECTION_GALLERY)->first();

        $componente->call('borrarImagen', $media->uuid);

        self::assertCount(1, $seccion->fresh()->getMedia(UsuarioCvSeccion::MEDIA_COLLECTION_GALLERY));
    }

    #[Test]
    public function sin_permiso_de_editar_no_puede_guardar(): void
    {
        $usuarioActivo = $this->usuarioConPermisos();
        $seccion = $this->crearSeccion();
        $cv = $seccion->usuarioCv;

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $cv->usuario])
            ->call('abrir', $cv->ulid)
            ->set('titulo', 'Nuevo título')
            ->call('guardar')
            ->assertForbidden();
    }

    #[Test]
    public function pide_confirmar_descarte_al_intentar_cambiar_de_seccion_con_cambios_sin_guardar(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();
        $primera = UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 0]);
        $segunda = UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 1]);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->set('titulo', 'Editado sin guardar')
            ->call('seleccionarSeccion', $segunda->ulid)
            ->assertDispatched('secciones-abrir-confirmar-descarte')
            ->assertSet('seccionSeleccionadaUlid', $primera->ulid)
            ->call('confirmarDescarte')
            ->assertSet('seccionSeleccionadaUlid', $segunda->ulid)
            ->assertSet('hayCambiosSinGuardar', false)
            ->assertDispatched('secciones-confirmar-descarte-ocultar');
    }

    #[Test]
    public function pide_confirmar_descarte_al_intentar_crear_seccion_con_cambios_sin_guardar(): void
    {
        $usuarioActivo = $this->usuarioConPermisos([
            PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION,
            PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION,
        ]);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();
        UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 0]);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->set('titulo', 'Editado sin guardar')
            ->call('crearSeccionVacia')
            ->assertNotDispatched('secciones-actualizadas')
            ->assertDispatched('secciones-abrir-confirmar-descarte');

        self::assertSame(1, $cv->secciones()->count());
    }

    #[Test]
    public function pide_confirmar_descarte_al_intentar_cerrar_el_modal_con_cambios_sin_guardar(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();
        UsuarioCvSeccion::factory()->for($cv, 'usuarioCv')->create(['orden' => 0]);

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->set('titulo', 'Editado sin guardar')
            ->call('intentarCerrarModal')
            ->assertNotDispatched('secciones-cerrar')
            ->assertDispatched('secciones-abrir-confirmar-descarte')
            ->call('confirmarDescarte')
            ->assertDispatched('secciones-cerrar')
            ->assertSet('hayCambiosSinGuardar', false);
    }

    #[Test]
    public function cierra_el_modal_directamente_si_no_hay_cambios_sin_guardar(): void
    {
        $usuarioActivo = $this->usuarioConPermisos(PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION);
        $usuarioObjetivo = Usuario::factory()->create();
        $cv = UsuarioCv::factory()->for($usuarioObjetivo, 'usuario')->create();

        Livewire::actingAs($usuarioActivo)
            ->test(UsuarioCvSeccionesModalLivewire::class, ['usuario' => $usuarioObjetivo])
            ->call('abrir', $cv->ulid)
            ->call('intentarCerrarModal')
            ->assertDispatched('secciones-cerrar')
            ->assertNotDispatched('secciones-abrir-confirmar-descarte');
    }
}
