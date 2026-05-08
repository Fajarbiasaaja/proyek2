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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('technician_id')->constrained('technicians')->onDelete('cascade');
            $table->unsignedTinyInteger('rating')->default(5); // 1-5 stars
            $table->text('review')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index untuk query performa
            $table->index('booking_id');
            $table->index('customer_id');
            $table->index('technician_id');
            $table->index(['technician_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
