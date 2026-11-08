<?php

use App\Models\Role;
use App\Models\User;
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
        // 1. Create or ensure super-admin role exists
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Administrador',
                'description' => 'Acceso y control total del sistema. Único rol con facultades para crear, promover y revocar administradores.'
            ]
        );

        // 2. Attach super-admin role to primary admin user
        $primaryAdmin = User::where('email', 'admin@showfuture.com')->first() ?? User::first();
        if ($primaryAdmin && !$primaryAdmin->roles()->where('slug', 'super-admin')->exists()) {
            $primaryAdmin->roles()->attach($superAdminRole->id);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $role = Role::where('slug', 'super-admin')->first();
        if ($role) {
            $role->users()->detach();
            $role->delete();
        }
    }
};
