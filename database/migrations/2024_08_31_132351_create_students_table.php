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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lrn')->unique(); 
            $table->string('firstname'); 
            $table->string('lastname');
            
            $table->string('sex')->nullable();
            $table->unsignedBigInteger('grade_id');
            $table->foreign('grade_id')->references('id')->on('grade');
            $table->unsignedBigInteger('section_id');
            $table->foreign('section_id')->references('id')->on('section');
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
