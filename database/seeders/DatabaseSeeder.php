<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@solar.gt')],
            ['name' => 'Administrador', 'password' => env('ADMIN_PASSWORD', 'Solar2026Gt')],
        );

        $this->call([DepartamentoSeeder::class, DemoSeeder::class]);
    }
}
