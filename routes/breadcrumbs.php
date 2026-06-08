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

// Inicio > Usuarios > Ver
Breadcrumbs::for('panel.usuarios.show', function (BreadcrumbTrail $trail, $usuario) {
    $trail->parent('panel.usuarios.index');
    $trail->push($usuario->nombre_completo, route('panel.usuarios.show', $usuario));
});

// Inicio > Usuarios > Crear
Breadcrumbs::for('panel.usuarios.create', function (BreadcrumbTrail $trail) {
    $trail->parent('panel.usuarios.index');
    $trail->push(trans('actions.create'), route('panel.usuarios.create'));
});

// Inicio > Usuarios > [Nombre completo] > Editar
Breadcrumbs::for('panel.usuarios.edit', function (BreadcrumbTrail $trail, $usuario) {
    $trail->parent('panel.usuarios.show', $usuario);
    $trail->push(trans('actions.edit'), route('panel.usuarios.edit', $usuario));
});

// Inicio > Roles
Breadcrumbs::for('panel.roles.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(trans('fields.roles.titulo'), route('panel.roles.index'));
});

// Inicio > Roles > [Nombre]
Breadcrumbs::for('panel.roles.show', function (BreadcrumbTrail $trail, $rol) {
    $trail->parent('panel.roles.index');
    $trail->push($rol->name, route('panel.roles.show', $rol));
});

// Inicio > Roles > Crear
Breadcrumbs::for('panel.roles.create', function (BreadcrumbTrail $trail) {
    $trail->parent('panel.roles.index');
    $trail->push(trans('actions.create'), route('panel.roles.create'));
});

// Inicio > Roles > [Nombre] > Editar
Breadcrumbs::for('panel.roles.edit', function (BreadcrumbTrail $trail, $rol) {
    $trail->parent('panel.roles.show', $rol);
    $trail->push(trans('actions.edit'), route('panel.roles.edit', $rol));
});
