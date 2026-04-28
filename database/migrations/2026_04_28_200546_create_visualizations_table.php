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
        Schema::create('visualizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dataset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dashboard_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('type'); // bar, line, pie, ...
            $table->json('config')->nullable(); // axes mapping, colors, filters
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visualizations');
    }
};
