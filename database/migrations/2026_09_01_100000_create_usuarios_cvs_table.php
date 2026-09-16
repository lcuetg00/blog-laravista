<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla usuarios_cvs (un usuario puede tener varios currículums) con ulid público, fk a usuarios y nombre del CV.
     */
    public function up(): void
    {
        Schema::create('usuarios_cvs', function (Blueprint $table) {
            $table->increments('id');
            // ULID público usado en URLs y route model binding
            $table->ulid('ulid')->unique();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Elimina la tabla usuarios_cvs.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_cvs');
    }
};
