<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Administrador',
                'slug' => 'super-admin',
                'description' => 'Control total del sistema. Único rol con facultades para nombrar y revocar administradores.',
            ],
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Acceso administrativo y gestión operativa de empleados y cuentas.',
            ],
            [
                'name' => 'Account Manager',
                'slug' => 'account-manager',
                'description' => 'Gestión de cuentas de clientes, onboarding y coordinación de tickets.',
            ],
            [
                'name' => 'Ads Manager',
                'slug' => 'ads-manager',
                'description' => 'Gestión y optimización de campañas de publicidad.',
            ],
            [
                'name' => 'Copywriter',
                'slug' => 'copywriter',
                'description' => 'Redacción de copies y estrategias de mensaje.',
            ],
            [
                'name' => 'Diseñador / Creativo',
                'slug' => 'creative',
                'description' => 'Diseño gráfico, banners y recursos visuales.',
            ],
            [
                'name' => 'Comercial',
                'slug' => 'commercial',
                'description' => 'Ventas y prospectos.',
            ],
            [
                'name' => 'Editor de Video',
                'slug' => 'editor',
                'description' => 'Edición y postproducción de video.',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }

        // Create Default Admin User
        $adminRole = Role::where('slug', 'admin')->first();
        $amRole = Role::where('slug', 'account-manager')->first();

        $admin = User::firstOrCreate(
            ['email' => 'admin@showfuture.com'],
            [
                'name' => 'Juan José Arévalo',
                'password' => Hash::make('password'),
                'phone' => '+57 300 000 0000',
                'is_active' => true,
            ]
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id, $amRole->id]);

        // Create a test Account Manager
        $am = User::firstOrCreate(
            ['email' => 'am@showfuture.com'],
            [
                'name' => 'Camila Account Manager',
                'password' => Hash::make('password'),
                'phone' => '+57 311 111 2222',
                'is_active' => true,
            ]
        );
        $am->roles()->syncWithoutDetaching([$amRole->id]);

        // Create a test Specialist
        $ads = User::firstOrCreate(
            ['email' => 'ads@showfuture.com'],
            [
                'name' => 'Carlos Ads Specialist',
                'password' => Hash::make('password'),
                'phone' => '+57 322 333 4444',
                'is_active' => true,
            ]
        );
        $adsRole = Role::where('slug', 'ads-manager')->first();
        $ads->roles()->syncWithoutDetaching([$adsRole->id]);
    }
}
