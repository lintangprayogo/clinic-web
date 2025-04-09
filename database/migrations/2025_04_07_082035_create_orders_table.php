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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->on('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->on('users')->onDelete('cascade');
            $table->string('service');
            $table->integer('price');
            $table->string('payment_url')->nullable();
            $table->string('status')->default('waiting');
            $table->integer('duration');
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->dateTime('schedule');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
