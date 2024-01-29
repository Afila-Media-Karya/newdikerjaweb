<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new Admin();
        $user->username = 'admin_keuangan';
        $user->password = Hash::make('password');
        $user->role = '3';
        $user->status = true;
        $user->save();
    }
}
