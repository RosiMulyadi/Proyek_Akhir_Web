<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Membuat user
        $user = User::create([
            'name' => 'SuperAdmin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('superadmin01')
        ]);

        // Membuat role "Admin" jika belum ada
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Membuat role "Pemilik" jika belum ada
        $pemilikRole = Role::firstOrCreate(['name' => 'Pemilik']);

        // Membuat role "Penyewa" jika belum ada
        $penyewaRole = Role::firstOrCreate(['name' => 'Penyewa']);

        // Assign role "Pemilik" ke user
        $user->assignRole([$pemilikRole->id]);

        // Assign role "Penyewa" ke user
        // $user->assignRole([$penyewaRole->id]); // Uncomment jika ingin memberikan role "Penyewa" ke user
    }
}
