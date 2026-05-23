<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade el campo "descripcion" (string 255, nullable) a las tablas roles y permissions.
     */
    public function up(): void
    {
        Schema::table(config('permission.table_names.roles'), function (Blueprint $table) {
            $table->string('descripcion', 255)->nullable()->after('name');
        });

        Schema::table(config('permission.table_names.permissions'), function (Blueprint $table) {
            $table->string('descripcion', 255)->nullable()->after('name');
        });
    }

    /**
     * Elimina el campo "descripcion" de las tablas roles y permissions.
     */
    public function down(): void
    {
        Schema::table(config('permission.table_names.roles'), function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });

        Schema::table(config('permission.table_names.permissions'), function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });
    }
};
