<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Lista de tablas que necesitan resetear sus secuencias
        $tables = [
            'payments',
            'beneficiaries',
            'vouchers',
            'projects',
            'plans',
            'readjustments',
            'settlements',
            'users'
        ];

        foreach ($tables as $table) {
            // En PostgreSQL, el nombre de la secuencia sigue el patrón: tabla_id_seq
            $sequence = "{$table}_id_seq";

            // Verificar si existe la secuencia
            $sequenceExists = DB::select("
                SELECT EXISTS (
                    SELECT 1
                    FROM pg_sequences
                    WHERE schemaname = 'public'
                    AND sequencename = ?
                )", [$sequence]);

            if ($sequenceExists[0]->exists) {
                // Obtener el máximo ID de la tabla
                $maxId = DB::table($table)->max('id') ?? 1;

                // Resetear la secuencia al máximo ID
                DB::statement("SELECT SETVAL(?, ?)", [$sequence, $maxId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario revertir este cambio
    }
};
