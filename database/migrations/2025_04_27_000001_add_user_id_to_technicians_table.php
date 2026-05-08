<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add user_id to technicians table
 * 
 * Adds a foreign key relationship between technicians and users table.
 * This links each technician to their corresponding user account (role='technician').
 * 
 * Relationship:
 * - technicians.user_id -> users.id
 * - One user (technician) has one technician record
 * - One technician belongs to one user account
 */
return new class extends Migration
{
    /**
     * Run the migrations - add user_id column
     */
    public function up(): void
    {
        Schema::table('technicians', function (Blueprint $table) {
            // Add user_id foreign key column
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            
            // Create foreign key constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations - drop user_id column
     */
    public function down(): void
    {
        Schema::table('technicians', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
