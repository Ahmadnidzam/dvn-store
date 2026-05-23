<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Buat akun admin dari konfigurasi .env.
     * Admin TIDAK bisa register lewat form — hanya via seeder ini.
     */
    public function run(): void
    {
        $email = config('dvnstore.admin.email');
        $name  = config('dvnstore.admin.name');
        $pass  = config('dvnstore.admin.password');

        if (empty($email) || empty($name) || empty($pass)) {
            $this->command->warn('AdminSeeder: konfigurasi admin di .env kosong. Lewati.');
            return;
        }

        Pengguna::updateOrCreate(
            ['email' => $email],
            [
                'name'              => $name,
                'password'          => Hash::make($pass),
                'role'              => 'admin',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("Admin '{$name}' <{$email}> berhasil dibuat / di-update.");
    }
}
