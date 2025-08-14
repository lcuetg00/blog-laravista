<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Devuelve el home de la aplicación
     */
    public function index(): Renderable
    {
        return view('home');
    }
}
