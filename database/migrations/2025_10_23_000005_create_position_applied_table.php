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
    Schema::create('position_applied', function (Blueprint $table) { // UBAH nama table
        $table->id();
        $table->foreignId('available_position_id')->constrained('available_position')->onDelete('cascade');
        $table->foreignId('society_id')->constrained('society')->onDelete('cascade');
        $table->date('apply_date');
        $table->enum('status', ['PENDING', 'ACCEPTED', 'REJECTED'])->default('PENDING');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
