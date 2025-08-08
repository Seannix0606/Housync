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
        Schema::create('rfid_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_uid')->unique(); // RFID card UID from scanner
            $table->string('card_number')->nullable(); // Human readable card number
            $table->foreignId('tenant_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('apartment_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'inactive', 'suspended', 'lost'])->default('active');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('access_common_areas')->default(true);
            $table->boolean('access_building')->default(true);
            $table->boolean('access_parking')->default(false);
            $table->json('access_schedule')->nullable(); // Store time-based access rules
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'status']);
            $table->index(['landlord_id', 'status']);
            $table->index(['unit_id', 'status']);
            $table->index(['apartment_id', 'status']);
            $table->index('card_uid');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_cards');
    }
};
