<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla paginas (CRUD del panel para las páginas públicas) con ulid público, clave estable única, título y descripción JSON traducibles vía spatie/laravel-translatable y flag de activación.
     */
    public function up(): void
    {
        Schema::create('paginas', function (Blueprint $table) {
            $table->bigIncrements('id');
            // ULID público usado en URLs y route model binding
            $table->ulid('ulid')->unique();
            // Clave estable que enlaza la fila con su página pública (home, creditos, ...). Inmutable desde el panel
            $table->string('clave', 50)->unique();
            // Columnas JSON traducibles vía spatie/laravel-translatable: {es, en, ja}
            $table->json('titulo');
            $table->json('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Elimina la tabla paginas.
     */
    public function down(): void
    {
        Schema::dropIfExists('paginas');
    }
};
