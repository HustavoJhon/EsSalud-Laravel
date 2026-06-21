<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'procedure.view',
            'procedure.create',
            'procedure.update',
            'procedure.submit',
            'procedure.approve',
            'procedure.reject',
            'procedure.subsanar',
            'procedure.assign',
            'document.upload',
            'document.view',
            'document.validate',
            'news.view',
            'news.create',
            'news.update',
            'news.delete',
            'faq.view',
            'faq.create',
            'faq.update',
            'faq.delete',
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'chat.use',
            'report.view',
            'settings.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm, 'guard_name' => 'web']);
        }

        $aseg = Role::create(['name' => 'ASEG', 'guard_name' => 'web']);
        $aseg->givePermissionTo([
            'procedure.view',
            'procedure.create',
            'procedure.submit',
            'procedure.subsanar',
            'document.upload',
            'document.view',
            'news.view',
            'faq.view',
            'chat.use',
        ]);

        $oper = Role::create(['name' => 'OPER', 'guard_name' => 'web']);
        $oper->givePermissionTo([
            'procedure.view',
            'procedure.approve',
            'procedure.reject',
            'procedure.subsanar',
            'document.upload',
            'document.view',
            'document.validate',
            'news.view',
            'faq.view',
            'chat.use',
            'report.view',
        ]);

        $supv = Role::create(['name' => 'SUPV', 'guard_name' => 'web']);
        $supv->givePermissionTo([
            'procedure.view',
            'procedure.approve',
            'procedure.reject',
            'procedure.assign',
            'procedure.subsanar',
            'document.upload',
            'document.view',
            'document.validate',
            'news.view',
            'faq.view',
            'faq.create',
            'faq.update',
            'user.view',
            'chat.use',
            'report.view',
        ]);

        $gesdoc = Role::create(['name' => 'GESDOC', 'guard_name' => 'web']);
        $gesdoc->givePermissionTo([
            'procedure.view',
            'procedure.approve',
            'procedure.reject',
            'procedure.assign',
            'document.upload',
            'document.view',
            'document.validate',
            'news.view',
            'news.create',
            'news.update',
            'faq.view',
            'faq.create',
            'faq.update',
            'faq.delete',
            'user.view',
            'chat.use',
            'report.view',
        ]);

        $sadm = Role::create(['name' => 'SADM', 'guard_name' => 'web']);
        $sadm->givePermissionTo(Permission::all());
    }
}
