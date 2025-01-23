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
        Schema::create('contract_clients', function (Blueprint $table) {
            $table->id();


            $table->string('signature_client');

            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');




            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_clients');
    }
};
