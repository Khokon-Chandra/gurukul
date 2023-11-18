<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserUpdatePasswordTest extends TestCase
{
    /**
     * test that only auth user can update password
     */
    public function testThatOnlyAuthenticatedUserCanChangePassword(): void
    {
        $response = $this->postJson(route('user.change.password'));
        $response->assertStatus(401);
    }


    /**
     * test that auth user can update password
     */
    public function testThatUserCanUpdatePassword(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('user.change.password'), [
            'password' => "Password#222",
            'password_confirmation' => "Password#222"
        ]);

        $this->assertTrue(Hash::check('Password#222', $this->user->password));
        $this->assertFalse(Hash::check('password', $this->user->password));

        $response->assertOk();
        $response->assertJsonStructure([
            "status",
            "message",
            "data"
        ]);
    }
}
