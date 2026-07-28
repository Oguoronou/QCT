<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_police_declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->unique()->constrained('items')->cascadeOnDelete();
            $table->foreignId('commissariat_id')->constrained('commissariats');
            $table->foreignId('declared_by_user_id')->constrained('users');
            $table->string('declaration_number');
            $table->string('receipt_photo')->nullable();
            $table->timestamp('declared_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_police_declarations');
    }
};
