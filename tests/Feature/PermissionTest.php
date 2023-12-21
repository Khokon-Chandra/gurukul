<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\FeatureBaseCase;
use Tests\TestCase;

class PermissionTest extends FeatureBaseCase
{
    /**
     * A basic feature test example.
     */
    public function testPermissionList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()->create();

        $user->givePermissionTo('user.access.users.permissions.index');

        $response = $this->actingAs($user)->getJson(route('users.permissions.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'users' => [
            ]
        ]);
    }



    public function testUserCanUpdatePermission(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()->create();

        $user->givePermissionTo('user.access.users.permissions.update');

        $response = $this->actingAs($user)->putJson(route('users.permissions.update', 1), [
            'name' => 'update from test',
            'parent_id' => null
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
            ]
        ]);
    }
}
