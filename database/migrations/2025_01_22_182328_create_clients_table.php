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
            $table->string('img')->nullable();



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
            $table->string('mobile_wallet')->nullable();
            $table->string('account_number_bank')->nullable();


            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('marital_status', ['single', 'married'])->nullable();
            $table->enum('status', ['active', 'inactive', 'canceled'])->default('active');
            $table->enum('refund', ['done', 'not'])->default('not');


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
