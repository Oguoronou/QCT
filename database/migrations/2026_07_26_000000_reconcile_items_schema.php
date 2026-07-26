<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Colonnes historiquement déclarées en enum restreint, mais dont le
     * code applicatif écrit des valeurs supplémentaires (claimed,
     * ownership_claimed, delivered, returned). On les convertit en texte
     * libre. Pas de dépendance à doctrine/dbal : SQL brut.
     */
    private array $columns = ['status', 'lost_found_status'];

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->columns as $column) {
            if ($this->isEnum($column)) {
                DB::statement("ALTER TABLE items MODIFY {$column} VARCHAR(50) NOT NULL");
            }
        }
    }

    public function down(): void
    {
        // Volontairement irréversible : revenir à un enum restreint
        // casserait les lignes existantes portant claimed/ownership_claimed/
        // delivered/returned.
    }

    private function isEnum(string $column): bool
    {
        $row = DB::selectOne(
            'SELECT DATA_TYPE as type FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [DB::getDatabaseName(), 'items', $column]
        );

        return $row !== null && strtolower($row->type) === 'enum';
    }
};
