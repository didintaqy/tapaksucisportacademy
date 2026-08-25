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
            $table->string('full_name');
            $table->string('nickname')->nullable();

            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();

            $table->string('nik', 16)->unique()->nullable();
            $table->string('family_card_number', 16)->nullable();

            $table->string('phone_number')->nullable();

            $table->enum('gender', ['L', 'P'])->nullable();

            $table->string('belt_level')->nullable();

            $table->text('address')->nullable();

            $table->string('education')->nullable();

            $table->string('photo')->nullable();

            $table->timestamps();
            $table->softDeletes();
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
