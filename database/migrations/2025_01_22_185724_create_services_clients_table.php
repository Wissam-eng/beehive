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
        Schema::create('services_clients', function (Blueprint $table) {
            $table->id();

            $table->string('service_name');
            $table->string('service_cost');
            $table->integer('order_id')->nullable();
            $table->integer('trans_id')->nullable();

            // $table->foreignId('service_id')->constrained('services')->onDelete('cascade');

            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            $table->enum('refund', ['paid', 'not_paid'])->default('not_paid');


            $table->enum('payment_status', ['paid', 'unpaid', 'pending'])->default('unpaid');
            $table->enum('status', ['active', 'inactive', 'canceled'])->default('inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_clients');
    }
};
