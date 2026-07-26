<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Optimisations pour la production
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Ajouter les indexes pour les recherches et filtres
            if (!Schema::hasColumn('items', 'item_name')) {
                return;
            }

            $table->index('user_id');
            $table->index('status');
            $table->index('lost_found_status');
            $table->index('created_at');
            $table->index(['category_name', 'status']);
            $table->fullText(['item_name', 'description']); // Pour les recherches full-text
        });

        // Ajouter cascade delete pour les users
        Schema::table('items', function (Blueprint $table) {
            // Note: Ces modifications dépendent de la base de données existante
            // Appliquer manuellement si nécessaire
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('items_user_id_index');
            $table->dropIndex('items_status_index');
            $table->dropIndex('items_lost_found_status_index');
            $table->dropIndex('items_created_at_index');
            $table->dropIndex('items_category_name_status_index');
            $table->dropFullText('items_item_name_description_fulltext');
        });
    }
};
