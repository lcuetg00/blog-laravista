<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla bloques (cada página se compone de bloques ordenados) con ulid público, fk a paginas, tipo y orden tinyint y un JSON campos con el contenido heterogéneo (algunos campos traducibles {es, en, ja}).
     */
    public function up(): void
    {
        Schema::create('bloques', function (Blueprint $table) {
            $table->bigIncrements('id');
            // ULID público usado en URLs y route model binding
            $table->ulid('ulid')->unique();
            // Página a la que pertenece el bloque: si se borra la página, sus bloques caen con ella
            $table->foreignId('pagina_id')->constrained('paginas')->cascadeOnDelete();
            // Tipo de bloque (BloqueTipoEnum, int backing) y posición dentro de la página
            $table->unsignedTinyInteger('tipo');
            $table->unsignedTinyInteger('orden');
            // Contenido del bloque: claves traducibles guardan {es, en, ja}, el resto valores escalares
            $table->json('campos');
            $table->datetimes();
            $table->softDeletes();

            // Una página no tendrá dos bloques con el mismo orden (defensa de integridad)
            $table->unique(['pagina_id', 'orden']);
        });
    }

    /**
     * Elimina la tabla bloques.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloques');
    }
};
