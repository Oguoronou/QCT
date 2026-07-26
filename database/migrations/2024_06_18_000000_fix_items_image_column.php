<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Renommer 'image' en 'images'
            if (Schema::hasColumn('items', 'image')) {
                $table->renameColumn('image', 'images');
            }

            // Ajouter cascade delete pour user_id
            if (!Schema::hasColumn('items', 'found_user_id')) {
                $table->foreignId('found_user_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'images')) {
                $table->renameColumn('images', 'image');
            }
        });
    }
};
