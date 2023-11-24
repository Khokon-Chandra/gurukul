<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
<<<<<<< HEAD
use Tests\TestCase;
=======
use Tests\FeatureBaseCase;
>>>>>>> f0d1bdc44b03289ee5c3290bf752ff7d0c9894ea

class UserUpdatePasswordTest extends FeatureBaseCase
{
    /**
     * test that only auth user can update password
     */
    public function testThatOnlyAuthenticatedUserCanChangePassword(): void
    {
<<<<<<< HEAD
        $response = $this->patchJson(route('user.change.password'));
=======
        $this->artisan('migrate:fresh --seed');

        $response = $this->putJson(route('user.change.password'));
>>>>>>> f0d1bdc44b03289ee5c3290bf752ff7d0c9894ea
        $response->assertStatus(401);
    }


    /**
     * test that auth user can update password
     */
    public function testThatUserCanUpdatePassword(): void
    {
<<<<<<< HEAD

        $response = $this->actingAs($this->user)->patchJson(route('user.change.password'), [
=======
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        $response = $this->actingAs($user)
            ->putJson(route('user.change.password'), [
>>>>>>> f0d1bdc44b03289ee5c3290bf752ff7d0c9894ea
            'password' => "Password#222",
            'password_confirmation' => "Password#222"
        ]);

<<<<<<< HEAD

        $this->assertDatabaseHas('users', [
            'password' => $this->user->password
        ]);

        $this->assertTrue(Hash::check('Password#222', $this->user->password));
        $this->assertFalse(Hash::check('password', $this->user->password));
=======
        $user = $user->refresh();
        $this->assertTrue(Hash::check('Password#222', $user->password));
        $this->assertFalse(Hash::check('password', $user->password));
>>>>>>> f0d1bdc44b03289ee5c3290bf752ff7d0c9894ea


        $response->assertOk();
<<<<<<< HEAD
        $response->assertJsonStructure([
            "status",
            "message",
            "data",
            "permissions"
=======
        $response->assertJson([
            'status' => 'successful',
>>>>>>> f0d1bdc44b03289ee5c3290bf752ff7d0c9894ea
        ]);
    }
}
