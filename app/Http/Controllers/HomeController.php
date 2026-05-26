<?php

namespace App\Http\Controllers;

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
        return view('public.home.creditos');
    }

    /**
     * Devuelve la página de tecnologías
     */
    public function tecnologias(): Renderable
    {
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
        $seo = [
            'title' => trans('public.contacto.titulo'),
            'description' => trans('public.contacto.descripcion'),
        ];

        return view('public.home.contacto', compact('seo'));
    }
}
