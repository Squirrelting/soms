major_offense_id<?php

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
        Schema::create('submitted_major_offenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lrn');
            $table->foreign('lrn')->references('lrn')->on('students');
            
            $table->string('student_firstname');
            $table->string('student_lastname');
            $table->string('student_grade');
            $table->string('student_section');
            $table->string('student_sex')->nullable();
            $table->tinyInteger('sanction')->default(0);
            $table->timestamp('cleansed_date')->nullable();

            $table->unsignedBigInteger('major_offense_id');
            $table->foreign('major_offense_id')->references('id')->on('major_offenses');

            $table->unsignedBigInteger('major_penalty_id');
            $table->foreign('major_penalty_id')->references('id')->on('major_penalties');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submitted_major_offenses');
    }
};
