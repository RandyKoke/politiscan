<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */


public function up()
{
    Schema::create('results', function (Blueprint $table) {
        $table->id();
        $table->foreignId('session_id')->constrained('questionnaire_sessions')->onDelete('cascade');
        $table->foreignId('party_id')->constrained()->onDelete('cascade');
        $table->decimal('score', 5, 2); // %
        $table->timestamps();
    });
}

/**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
