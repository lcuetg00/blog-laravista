<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade la columna "ulid" (única, justo después del id) a las tablas
     * de los modelos que se expondrán públicamente. El id numérico se mantiene
     * como PK para rendimiento; el ulid se usa como identificador en las URLs.
     */
    public function up(): void
    {
        // Añadimos el ulid a la tabla de usuarios
        Schema::table('usuarios', function (Blueprint $table) {
            $table->ulid('ulid')->unique()->after('id');
        });

        // Añadimos el ulid a la tabla de roles
        Schema::table(config('permission.table_names.roles'), function (Blueprint $table) {
            $table->ulid('ulid')->unique()->after('id');
        });

        // Añadimos el ulid a la tabla de permissions
        Schema::table(config('permission.table_names.permissions'), function (Blueprint $table) {
            $table->ulid('ulid')->unique()->after('id');
        });
    }

    /**
     * Elimina la columna "ulid" de las tablas afectadas.
     */
    public function down(): void
    {
        // Eliminamos la columna ulid en orden inverso
        Schema::table(config('permission.table_names.permissions'), function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });

        Schema::table(config('permission.table_names.roles'), function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};
