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
<<<<<<< HEAD
    public $user;

    public function setUp():void {
        parent::setUp();

        $this->user =  User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $this->user->assignRole(Role::where('name', 'Administrator')->first());


    }
=======
>>>>>>> f0d1bdc44b03289ee5c3290bf752ff7d0c9894ea
}
