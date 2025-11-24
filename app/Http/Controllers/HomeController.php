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
        return view('home');
    }

    /**
     * Devuelve la página de créditos
     */
    public function credits(): Renderable
    {
        return view('credits');
    }

    /**
     * Devuelve la página de tecnologías
     */
    public function tecnologias(): Renderable
    {
        return view('tecnologias');
    }

    /**
     * Devuelve la página de proyectos
     */
    public function proyectos(): Renderable
    {
        return view('proyectos');
    }

    /**
     * Devuelve la página de contacto
     */
    public function contacto(): Renderable
    {
        return view('contacto');
    }
}
