<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Midtrans Payment Gateway Fields to Payments Table
 * 
 * Purpose: Add fields untuk mendukung payment gateway integration (Midtrans)
 * - transaction_id: Unique transaction ID dari Midtrans
 * - payment_gateway: Payment gateway yang digunakan (midtrans, manual, etc)
 * - gateway_response: Full response dari payment gateway (JSON)
 * - gateway_status: Status dari gateway (pending, settlement, etc)
 * 
 * Backward Compatible: Existing payments tetap berjalan tanpa perubahan
 */
return new class extends Migration
{
    /**
     * Run the migrations
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Payment gateway identifier
            $table->string('payment_gateway')->default('manual')->after('payment_method')
                  ->comment('Payment gateway: midtrans, manual, etc');
            
            // Midtrans transaction ID
            $table->string('transaction_id')->nullable()->after('payment_gateway')
                  ->comment('Unique transaction ID dari payment gateway (Midtrans snap_token)');
            
            // Gateway response data (JSON)
            $table->json('gateway_response')->nullable()->after('transaction_id')
                  ->comment('Full response dari payment gateway (JSON format)');
            
            // Gateway status (different from payment status)
            $table->string('gateway_status')->nullable()->after('gateway_response')
                  ->comment('Gateway payment status: pending, settlement, failure, etc');
            
            // Midtrans redirect URL (for customer to complete payment)
            $table->string('payment_url')->nullable()->after('gateway_status')
                  ->comment('URL untuk customer redirect ke payment gateway');
            
            // Index untuk faster queries
            $table->index('transaction_id');
            $table->index('payment_gateway');
            $table->index('gateway_status');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['transaction_id']);
            $table->dropIndex(['payment_gateway']);
            $table->dropIndex(['gateway_status']);
            
            $table->dropColumn([
                'payment_gateway',
                'transaction_id',
                'gateway_response',
                'gateway_status',
                'payment_url',
            ]);
        });
    }
};
