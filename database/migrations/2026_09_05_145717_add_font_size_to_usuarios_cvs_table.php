<?php

use App\Enums\FontSizeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade "font_size_cabecera" y "font_size_contenido" a usuarios_cvs, para controlar por separado el tamaño de fuente de la cabecera y del resto del texto del PDF.
     */
    public function up(): void
    {
        Schema::table('usuarios_cvs', function (Blueprint $table) {
            $table->tinyInteger('font_size_cabecera')->unsigned()->default(FontSizeEnum::MEDIUM->value)->after('color_secundario');
            $table->tinyInteger('font_size_contenido')->unsigned()->default(FontSizeEnum::MEDIUM->value)->after('font_size_cabecera');
        });
    }

    /**
     * Elimina las columnas font_size_cabecera y font_size_contenido de usuarios_cvs.
     */
    public function down(): void
    {
        Schema::table('usuarios_cvs', function (Blueprint $table) {
            $table->dropColumn(['font_size_cabecera', 'font_size_contenido']);
        });
    }
};
