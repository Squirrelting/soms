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
        Schema::create('print_cgm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lrn'); 
            $table->string('firstname'); 
            $table->string('middlename')->nullable(); 
            $table->string('lastname');
            $table->string('generated_by');
            $table->string('signatory');
            $table->string('position');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_cgm');
    }
};
