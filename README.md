<p align="center">
  <img src="public/images/laravistaLogo.png" width="180" alt="Logo de Laravista">
</p>

<h1 align="center">Laravista</h1>

Sitio web personal multilingüe (español / inglés / japonés) con un panel de
administración desde el que se gestiona todo el contenido público sin tocar código.

## ¿Qué puede hacer este proyecto?

El proyecto tiene dos caras: la **web pública** que ven los visitantes y el
**panel** privado desde el que se administra todo.

### Público

- **Páginas compuestas por bloques** — cada página (inicio, créditos,
  tecnologías, proyectos, contacto) se monta con bloques de contenido: textos,
  imágenes, carruseles, tablas, botones… Ver [docs/paginas.md](docs/paginas.md).
- **Multilingüe (es / en / ja)** — todo el contenido y las URLs se muestran en
  los tres idiomas según el que elija el visitante.

### Panel

- **Acceso protegido** — autenticación con roles y permisos por acción.
- **Edición de páginas y bloques** — se cambian los textos e imágenes de cada
  bloque, se activan/desactivan páginas y se previsualiza el resultado en vivo.
  Ver [docs/paginas.md](docs/paginas.md).
- **Gestión de usuarios** — listado con búsqueda, filtros, ordenación,
  exportación a Excel y borrado recuperable.
- **Gestión de archivos e imágenes** — subida de imágenes asociadas a los
  bloques (carruseles, iconos, fotos) almacenadas de forma ordenada.

## Documentación detallada

Cada funcionalidad se explica en su propio documento dentro de `docs/`:

### Público

- [Páginas](docs/paginas.md)

### Panel

- [Páginas](docs/paginas.md)

## Stack

PHP 8.3+ · Laravel 13 · Livewire 4 · Bootstrap 5.3 + Tailwind 4 · Vite ·
MySQL/MariaDB · PHPUnit 12.

## Puesta en marcha

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed   # crea tablas y datos de ejemplo
composer dev                       # servidor + colas + logs + Vite
```

El entorno de desarrollo (`composer dev`) levanta el servidor, la cola, el visor
de logs (Pail) y Vite a la vez.
