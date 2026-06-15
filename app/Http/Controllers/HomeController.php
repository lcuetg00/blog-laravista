<?php

namespace App\Http\Controllers;

use App\Helpers\SeoHelper;
use App\Models\Pagina;
use App\Services\ConfiguracionService;
use Illuminate\Contracts\Support\Renderable;

class HomeController extends Controller
{
    /**
     * Devuelve el home de la aplicación
     */
    public function index(): Renderable
    {
        $pagina = $this->paginaConBloques('home');

        return view('public.home.home', ['pagina' => $pagina, 'seo' => SeoHelper::desdePagina($pagina)]);
    }

    /**
     * Devuelve la página de créditos
     */
    public function creditos(): Renderable
    {
        // Si la página está desactivada en el panel, no se puede acceder ni con URL directa
        abort_unless(ConfiguracionService::estaActiva('creditos'), 404);

        $pagina = $this->paginaConBloques('creditos');

        return view('public.home.creditos', ['pagina' => $pagina, 'seo' => SeoHelper::desdePagina($pagina)]);
    }

    /**
     * Devuelve la página de tecnologías
     */
    public function tecnologias(): Renderable
    {
        abort_unless(ConfiguracionService::estaActiva('tecnologias'), 404);

        $pagina = $this->paginaConBloques('tecnologias');

        return view('public.home.tecnologias', ['pagina' => $pagina, 'seo' => SeoHelper::desdePagina($pagina)]);
    }

    /**
     * Devuelve la página de proyectos
     */
    public function proyectos(): Renderable
    {
        abort_unless(ConfiguracionService::estaActiva('proyectos'), 404);

        $pagina = $this->paginaConBloques('proyectos');

        return view('public.home.proyectos', ['pagina' => $pagina, 'seo' => SeoHelper::desdePagina($pagina)]);
    }

    /**
     * Devuelve la página de contacto
     */
    public function contacto(): Renderable
    {
        abort_unless(ConfiguracionService::estaActiva('contacto'), 404);

        $pagina = $this->paginaConBloques('contacto');

        return view('public.home.contacto', ['pagina' => $pagina, 'seo' => SeoHelper::desdePagina($pagina)]);
    }

    /**
     * Devuelve la página de política de privacidad
     */
    public function politicaPrivacidad(): Renderable
    {
        abort_unless(ConfiguracionService::estaActiva('politica_privacidad'), 404);

        $seo = [
            'title' => trans('public.politica_privacidad.titulo'),
            'description' => trans('public.politica_privacidad.descripcion'),
        ];

        return view('public.home.politica-privacidad', compact('seo'));
    }

    /**
     * Devuelve la página de términos y condiciones
     */
    public function terminosCondiciones(): Renderable
    {
        abort_unless(ConfiguracionService::estaActiva('terminos_condiciones'), 404);

        $seo = [
            'title' => trans('public.terminos_condiciones.titulo'),
            'description' => trans('public.terminos_condiciones.descripcion'),
        ];

        return view('public.home.terminos-condiciones', compact('seo'));
    }

    /**
     * Carga una página por su clave con sus bloques y los media de cada bloque (eager load para evitar N+1 al renderizar).
     */
    private function paginaConBloques(string $clave): Pagina
    {
        return Pagina::with(['bloques.media'])->where('clave', $clave)->firstOrFail();
    }
}
