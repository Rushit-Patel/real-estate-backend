<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view clients', 'create clients', 'update clients', 'delete clients',
            'view property types', 'create property types', 'update property types', 'delete property-types',
            'view projects', 'create projects', 'update projects', 'delete projects', 'upload project files',
            'view enquiries', 'create enquiries', 'update enquiries', 'delete enquiries', 'assign enquiries', 'manage enquiry properties',
            'view bookings', 'create bookings', 'update bookings', 'delete bookings', 'cancel bookings',
            'view email templates', 'create email templates', 'update email templates', 'delete email templates',
            'view whatsapp templates', 'create whatsapp templates', 'update whatsapp templates', 'delete whatsapp templates', 'manage whatsapp variables',
            'view triggers', 'create triggers', 'update triggers', 'delete triggers',
            'view notifications', 'manage notifications',
            'view activity logs',
            'view enquiry sources', 'create enquiry sources', 'update enquiry sources', 'delete enquiry sources',
            'view enquiry statuses', 'create enquiry statuses', 'update enquiry statuses', 'delete enquiry statuses',
            'view variable types', 'create variable types', 'update variable types', 'delete variable types',
            'view users', 'create users', 'update users', 'delete users',
            'view roles', 'create roles', 'update roles', 'delete roles',
            'view permissions', 'create permissions', 'update permissions', 'delete permissions',
            'bulk actions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'view clients', 'view projects', 'view enquiries', 'view bookings',
            'view notifications',
        ]);
    }
}