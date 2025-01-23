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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();



            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->date('birth_date');
            $table->string('img');



            $table->string('mobile');
            $table->string('another_mobile');
            $table->string('na_number')->nullable();
            $table->string('age')->nullable();
            $table->string('city')->nullable();
            $table->string('work')->nullable();
            $table->string('center')->nullable();
            $table->string('landline')->nullable();
            $table->string('governorate')->nullable();
            $table->string('Village_Street')->nullable();
            $table->string('num_of_children')->nullable();
            $table->string('Academic_qualification')->nullable();


            $table->enum('gender', ['male', 'female']);
            $table->enum('marital_status', ['single', 'married']);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
