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
        Schema::create('submitted_minor_offenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lrn');
            $table->foreign('lrn')->references('lrn')->on('students');
            
            $table->string('student_name');
            $table->string('student_grade');
            $table->string('student_sex')->nullable();

            $table->unsignedBigInteger('minor_offense_id');
            $table->foreign('minor_offense_id')->references('id')->on('minor_offenses');

            $table->unsignedBigInteger('minor_penalty_id');
            $table->foreign('minor_penalty_id')->references('id')->on('minor_penalties');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submitted_minor_offenses');
    }
};

