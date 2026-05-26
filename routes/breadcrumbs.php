<?php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Diglactic\Breadcrumbs\Breadcrumbs;
// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Inicio (dashboard del panel)
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push(trans('breadcrumbs.home'), route('panel.index'));
});

// Inicio > Usuarios
Breadcrumbs::for('panel.usuarios.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(trans('fields.usuarios.titulo'), route('panel.usuarios.index'));
});
