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

// Inicio > Ajustes > Información del sitio
Breadcrumbs::for('panel.configuracion.informacion', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(trans('sidebar.settings'), route('panel.configuracion.informacion'));
    $trail->push(trans('configuracion.menu.informacion'), route('panel.configuracion.informacion'));
});

// Inicio > Ajustes > Parámetros
Breadcrumbs::for('panel.configuracion.parametros', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(trans('sidebar.settings'), route('panel.configuracion.informacion'));
    $trail->push(trans('configuracion.menu.parametros'), route('panel.configuracion.parametros'));
});

// Inicio > Ajustes > Mantenimiento
Breadcrumbs::for('panel.configuracion.mantenimiento', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(trans('sidebar.settings'), route('panel.configuracion.informacion'));
    $trail->push(trans('configuracion.menu.mantenimiento'), route('panel.configuracion.mantenimiento'));
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

// Inicio > Usuarios > [Nombre completo] > CVs
Breadcrumbs::for('panel.usuarios.cvs', function (BreadcrumbTrail $trail, $usuario) {
    $trail->parent('panel.usuarios.show', $usuario);
    $trail->push(trans('fields.usuarios_cvs.titulo'), route('panel.usuarios.cvs', $usuario));
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

// Inicio > Páginas
Breadcrumbs::for('panel.paginas.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(trans('fields.paginas.titulo'), route('panel.paginas.index'));
});

// Inicio > Páginas > [Título]
Breadcrumbs::for('panel.paginas.show', function (BreadcrumbTrail $trail, $pagina) {
    $trail->parent('panel.paginas.index');
    $trail->push($pagina->titulo ?: $pagina->clave, route('panel.paginas.show', $pagina));
});

// Inicio > Páginas > [Título] > Editar
Breadcrumbs::for('panel.paginas.edit', function (BreadcrumbTrail $trail, $pagina) {
    $trail->parent('panel.paginas.show', $pagina);
    $trail->push(trans('actions.edit'), route('panel.paginas.edit', $pagina));
});
