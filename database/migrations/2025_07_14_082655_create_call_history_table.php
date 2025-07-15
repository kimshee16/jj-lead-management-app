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
        Schema::create('call_history', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->unsignedBigInteger('lead_client_id')->nullable();
            $table->timestamp('call_timestamp');
            $table->string('call_status')->default('initiated'); // initiated, completed, failed
            $table->text('call_response')->nullable(); // Store API response
            $table->string('call_sid')->nullable(); // Twilio call SID if available
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('lead_client_id')->references('id')->on('lead_clients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_history');
    }
};
