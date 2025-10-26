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
    Schema::create('portofolio', function (Blueprint $table) { // UBAH nama table
        $table->id();
        $table->string('skill');
        $table->text('description')->nullable();
        $table->string('file')->nullable();
        $table->foreignId('society_id')->constrained('society')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portofolio');    // UBAH nama table
    }
};
