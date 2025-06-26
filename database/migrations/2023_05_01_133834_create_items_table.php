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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->foreignId("found_user_id")->nullable();
            $table->string("item_name");
            $table->string("category_name");
            $table->string("date");
            $table->string("image")->nullable();
            $table->longText("description");
            $table->enum("status",["lost", "found"]);
            $table->enum("lost_found_status",["pending", "found", "draft", "deliver"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
