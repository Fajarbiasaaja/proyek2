<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Link existing technician records to user accounts
 * 
 * This migration links existing technician records to their corresponding
 * user accounts by matching email addresses.
 * 
 * Logic:
 * - Find technician where user_id is null
 * - Match with user where role='technician' and email matches
 * - Update technician.user_id with the matching user.id
 */
return new class extends Migration
{
    /**
     * Run the migrations - link technicians to users
     */
    public function up(): void
    {
        // SQL query to update technician records with their corresponding user_id
        DB::statement('
            UPDATE technicians t
            INNER JOIN users u ON t.email = u.email
            SET t.user_id = u.id
            WHERE t.user_id IS NULL
            AND u.role = "technician"
        ');
    }

    /**
     * Reverse the migrations - unlink technicians from users
     */
    public function down(): void
    {
        DB::statement('
            UPDATE technicians
            SET user_id = NULL
            WHERE user_id IS NOT NULL
        ');
    }
};
