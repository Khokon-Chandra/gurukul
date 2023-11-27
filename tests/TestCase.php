<?php

namespace Tests;

<<<<<<< HEAD
=======
use App\Models\User;
>>>>>>> 29b218b3b52a34e2d08ee4e2615d72bd274ed351
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;
}
