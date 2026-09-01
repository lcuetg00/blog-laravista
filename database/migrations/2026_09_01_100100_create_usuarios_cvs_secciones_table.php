<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla usuarios_cvs_secciones (cada CV se compone de secciones ordenadas) con ulid público, fk a usuarios_cvs, orden y descripción que admite HTML.
     */
    public function up(): void
    {
        Schema::create('usuarios_cvs_secciones', function (Blueprint $table) {
            $table->increments('id');
            // ULID público usado en URLs y route model binding
            $table->ulid('ulid')->unique();
            // unsignedInteger (no foreignId, que crea unsignedBigInteger) para coincidir con el tipo de usuarios_cvs.id
            $table->unsignedInteger('usuario_cv_id');
            $table->foreign('usuario_cv_id')->references('id')->on('usuarios_cvs')->cascadeOnDelete();
            $table->string('titulo', 500);
            // Admite HTML escrito a mano (preparado para un futuro editor Summernote); sin sanitizar todavía
            $table->text('descripcion')->nullable();
            // Posición dentro del CV. unsignedSmallInteger (no tinyint) para dar margen al algoritmo de reordenación por desplazamiento
            $table->unsignedSmallInteger('orden');
            $table->datetimes();
            $table->softDeletes();

            // Un CV no tendrá dos secciones con el mismo orden (defensa de integridad)
            $table->unique(['usuario_cv_id', 'orden']);
        });
    }

    /**
     * Elimina la tabla usuarios_cvs_secciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_cvs_secciones');
    }
};
