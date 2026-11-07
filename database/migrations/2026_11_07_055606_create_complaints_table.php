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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title', 150);
            $table->text('description');
            $table->string('location_address', 255);
            $table->string('reporter_name', 100);
            $table->string('reporter_phone', 20);
            $table->string('reporter_email', 150)->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->string('status', 20)->default('baru');
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['category_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
