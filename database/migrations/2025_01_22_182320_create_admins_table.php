<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->date('birth_date');
            $table->string('img');




            $table->string('mobile');

            $table->string('age')->nullable();




            $table->enum('gender', ['male', 'female']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });


        // إضافة حساب الأدمن بعد إنشاء الجدول
        DB::table('admins')->insert([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('1'), // تشفير كلمة المرور
            'birth_date' => '1990-01-01',
            'img' => 'default.png',
            'mobile' => '123456789',
            'gender' => 'male',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
