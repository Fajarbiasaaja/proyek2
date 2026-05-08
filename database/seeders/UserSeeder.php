<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates test accounts for all roles:
     * - Admin (2 accounts)
     * - Customer (4 accounts)
     * - Technician (5 accounts)
     */
    public function run(): void
    {
        // ============================================
        // ADMIN ACCOUNTS (2)
        // ============================================
        
        User::create([
            'name' => 'Admin Super',
            'email' => 'admin@jasaku.com',
            'password' => Hash::make('Admin@123456'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Admin Secondary',
            'email' => 'admin2@jasaku.com',
            'password' => Hash::make('SecurePass123!'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // ============================================
        // CUSTOMER ACCOUNTS (4)
        // ============================================
        
        User::create([
            'name' => 'Customer One',
            'email' => 'customer1@example.com',
            'password' => Hash::make('Customer@123'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Budi Customer',
            'email' => 'budi@example.com',
            'password' => Hash::make('Budi@12345'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Siti Customer',
            'email' => 'siti@example.com',
            'password' => Hash::make('Siti@12345'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Test Customer',
            'email' => 'test.customer@example.com',
            'password' => Hash::make('TestPass@123'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        // ============================================
        // TECHNICIAN ACCOUNTS (5)
        // ============================================
        
        User::create([
            'name' => 'Ahmad Teknisi',
            'email' => 'ahmad.teknisi@provider.com',
            'password' => Hash::make('TechPass@123'),
            'role' => 'technician',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Budi Service',
            'email' => 'budi.service@provider.com',
            'password' => Hash::make('Service@123'),
            'role' => 'technician',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Doni Maintenance',
            'email' => 'doni.maintenance@provider.com',
            'password' => Hash::make('Maintain@123'),
            'role' => 'technician',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Roni Junior',
            'email' => 'roni.junior@provider.com',
            'password' => Hash::make('Junior@123'),
            'role' => 'technician',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Hendra Professional',
            'email' => 'hendra.pro@provider.com',
            'password' => Hash::make('ProTech@123'),
            'role' => 'technician',
            'email_verified_at' => now(),
        ]);
    }
}
