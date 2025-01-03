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
            $table->string('student_id', 15)->unique();
            $table->string('mykid', 30)->unique();
            $table->string('full_name');
            $table->string('email');
            $table->date('dob');
            $table->string('gender', 10)->comment('male/female');
            $table->integer('race');
            $table->integer('religion');
            $table->foreignId('parent_id')->constrained('users');
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
