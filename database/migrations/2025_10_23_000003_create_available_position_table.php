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
    Schema::create('available_position', function (Blueprint $table) { // UBAH nama table
        $table->id();
        $table->string('position_name');
        $table->integer('capacity');
        $table->text('description')->nullable();
        $table->date('submission_start_date');
        $table->date('submission_end_date');
        $table->foreignId('company_id')->constrained('company')->onDelete('cascade');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('available_position');    // UBAH nama table 
    }
};
