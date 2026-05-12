<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PanelController extends Controller
{
    /**
     * Muestra la página principal del panel de administración.
     */
    public function index(): View
    {
        return view('panel.dashboard.index');
    }
}
