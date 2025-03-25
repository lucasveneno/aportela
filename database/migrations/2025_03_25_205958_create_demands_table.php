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
        Schema::create('demands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Requester
            //$table->foreignId('area_id')->constrained()->onDelete('cascade'); // Demand Area
            $table->json('area_id')->nullable();
            $table->text('description');
            $table->string('status')->default('pending'); // pending, in progress, resolved
            $table->boolean('requires_councilor')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demands');
    }
};
