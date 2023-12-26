<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

use Tests\TestCase;

use Tests\FeatureBaseCase;


class UserUpdatePasswordTest extends FeatureBaseCase
{
    /**
     * test that only auth user can update password
     */
    public function testThatOnlyAuthenticatedUserCanChangePassword(): void
    {

        $this->artisan('migrate:fresh --seed');
        $response = $this->putJson(route('user.change.password', ['user' => 1]));
        $response->assertStatus(401);
    }


    /**
     * test that auth user can update password
     */
    public function testThatUserCanUpdatePassword(): void
    {

        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        $response = $this->actingAs($user)->putJson(route('user.change.password', ['user' => $user]), [
            'password' => "UserPassword#222",
            'password_confirmation' => "UserPassword#222"
        ]);


        $user = $user->refresh();

        $this->assertTrue(Hash::check('UserPassword#222', $user->password));
        $this->assertFalse(Hash::check('password', $user->password));



        $response->assertOk();

        $response->assertJson([
            'status' => 'successful',
        ]);
    }
}
