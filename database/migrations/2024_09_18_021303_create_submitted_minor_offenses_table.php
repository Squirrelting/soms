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
            
            $table->string('student_firstname');
            $table->string('student_middlename')->nullable();
            $table->string('student_lastname');
            $table->string('student_grade');
            $table->string('student_section');
            $table->string('student_sex')->nullable();
            $table->string('student_schoolyear');
            $table->string('student_quarter');
            $table->tinyInteger('sanction')->default(0);
            $table->timestamp('cleansed_date')->nullable();

            $table->string('minor_offense');
            $table->string('minor_penalty');

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

