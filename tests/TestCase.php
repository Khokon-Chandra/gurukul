<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;
    public $user;
    public $role;

    public function setUp():void {
        parent::setUp();

        $this->role = Role::create(['name' => 'Writer',]);

        $this->user =  User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();
        $this->user->assignRole('Writer');
        $permission = Permission::create(['name' => 'edit articles']);
        $this->role->givePermissionTo('edit articles');


    }
}
