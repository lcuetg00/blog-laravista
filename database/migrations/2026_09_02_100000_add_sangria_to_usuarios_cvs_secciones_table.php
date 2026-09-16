<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade "sangria" a usuarios_cvs_secciones: si está activo, el PDF muestra la descripción con una barra vertical y sangrado a la derecha.
     */
    public function up(): void
    {
        Schema::table('usuarios_cvs_secciones', function (Blueprint $table) {
            $table->boolean('sangria')->default(false)->after('descripcion');
        });
    }

    /**
     * Elimina la columna sangria de usuarios_cvs_secciones.
     */
    public function down(): void
    {
        Schema::table('usuarios_cvs_secciones', function (Blueprint $table) {
            $table->dropColumn('sangria');
        });
    }
};
