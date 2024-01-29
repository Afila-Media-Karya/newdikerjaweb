<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = new User();
        $user->id_pegawai = 1;
        $user->username = 'admin_opd';
        $user->password = Hash::make('password');
        $user->role = '1';
        $user->status = true;
        $user->save();
    }
}
