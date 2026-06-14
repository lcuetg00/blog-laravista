<?php

namespace App\Http\Controllers;

use App\Services\ConfiguracionService;
use Illuminate\Contracts\Support\Renderable;

class HomeController extends Controller
{
    /**
     * Devuelve el home de la aplicación
     */
    public function index(): Renderable
    {
        $seo = [
            'title' => trans('public.home.title'),
            'description' => trans('public.home.description'),
        ];

        return view('public.home.home', compact('seo'));
    }

    /**
     * Devuelve la página de créditos
     */
    public function creditos(): Renderable
    {
        // Si la página está desactivada en el panel, no se puede acceder ni con URL directa
        abort_unless(ConfiguracionService::estaActiva('creditos'), 404);

        return view('public.home.creditos');
    }

    /**
     * Devuelve la página de tecnologías
     */
    public function tecnologias(): Renderable
    {
        abort_unless(ConfiguracionService::estaActiva('tecnologias'), 404);

        $seo = [
            'title' => trans('public.tecnologias.titulo'),
            'description' => trans('public.tecnologias.descripcion'),
        ];

        return view('public.home.tecnologias', compact('seo'));
    }

    /**
     * Devuelve la página de proyectos
     */
    public function proyectos(): Renderable
    {
        abort_unless(ConfiguracionService::estaActiva('proyectos'), 404);

        $seo = [
            'title' => trans('public.proyectos.titulo'),
            'description' => trans('public.proyectos.descripcion'),
        ];

        return view('public.home.proyectos', compact('seo'));
    }

    /**
     * Devuelve la página de contacto
     */
    public function contacto(): Renderable
    {
        abort_unless(ConfiguracionService::estaActiva('contacto'), 404);

        $seo = [
            'title' => trans('public.contacto.titulo'),
            'description' => trans('public.contacto.descripcion'),
        ];

        return view('public.home.contacto', compact('seo'));
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
}
