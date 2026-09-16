<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade "color_primario" y "color_secundario" a usuarios_cvs (hex), usados como fondo de la cabecera y el pie del PDF.
     */
    public function up(): void
    {
        Schema::table('usuarios_cvs', function (Blueprint $table) {
            $table->string('color_primario', 7)->after('nombre');
            $table->string('color_secundario', 7)->after('color_primario');
        });
    }

    /**
     * Elimina las columnas color_primario y color_secundario de usuarios_cvs.
     */
    public function down(): void
    {
        Schema::table('usuarios_cvs', function (Blueprint $table) {
            $table->dropColumn(['color_primario', 'color_secundario']);
        });
    }
};
