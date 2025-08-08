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
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('card_uid')->nullable(); // RFID card UID
            $table->foreignId('rfid_card_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('tenant_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('apartment_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('location')->nullable(); // Entry point (e.g., "main_entrance", "parking_gate")
            $table->enum('action_type', ['card_scan', 'access_granted', 'access_denied', 'card_registered', 'card_deactivated'])->default('card_scan');
            $table->enum('access_result', ['granted', 'denied', 'unknown_card', 'inactive_card', 'expired_access', 'time_restricted'])->nullable();
            $table->string('device_id')->nullable(); // ESP32 device identifier
            $table->string('scanner_location')->nullable(); // Physical location of scanner
            $table->timestamp('scanned_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->json('additional_data')->nullable(); // Store ESP32 sensor data, etc.
            $table->ipAddress('device_ip')->nullable();
            $table->boolean('is_valid_scan')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index(['card_uid', 'scanned_at']);
            $table->index(['tenant_id', 'scanned_at']);
            $table->index(['action_type', 'scanned_at']);
            $table->index(['access_result', 'scanned_at']);
            $table->index(['device_id', 'scanned_at']);
            $table->index('scanned_at');
            $table->index(['apartment_id', 'scanned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
