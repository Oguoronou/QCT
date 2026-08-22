<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'google_id')) {
            DB::statement("ALTER TABLE users ADD google_id VARCHAR(255) NULL UNIQUE AFTER id");
        }

        DB::statement("ALTER TABLE users MODIFY mobile_no VARCHAR(255) NULL");
        DB::statement("ALTER TABLE users MODIFY country VARCHAR(255) NULL");
        DB::statement("ALTER TABLE users MODIFY city VARCHAR(255) NULL");
        DB::statement("ALTER TABLE users MODIFY password VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY mobile_no VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE users MODIFY country VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE users MODIFY city VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL");

        if (Schema::hasColumn('users', 'google_id')) {
            Schema::table('users', function ($table) {
                $table->dropColumn('google_id');
            });
        }
    }
};
