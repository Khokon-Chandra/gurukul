<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

abstract class FeatureBaseCase extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    /**
     * @var String
     */
    protected $loginToken;

    protected $headers = [];

    public $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
