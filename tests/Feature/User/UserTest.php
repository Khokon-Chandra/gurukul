<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\UserIp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\FeatureBaseCase;

class UserTest extends FeatureBaseCase
{
    /**
     * User List.
     */
    public function testUserRoleList(): void
    {
        $this->artisan('migrate:fresh --seed');


        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $role = Role::create(['name' => 'Admin']);
        $role->permissions()->sync([1, 2, 3]);


        $response = $this->actingAs($user)->getJson('/api/v1/user');


        $response->assertStatus(200);

        $response->assertJsonStructure([
            "data" => [
                '*' => [
                    'id',
                    'name',
                    'username',
                    'type',
                    'email',
                    'last_login_ip',
                    'join_date',
                    'active',
                    'created_at',
                    'role' => [
                        'id',
                        'name',
                        'created_at',
                    ]
                ]
            ],
            'meta' => [

            ],
            'links' => [

            ],
        ]);
    }


    public function testAllUserList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->getJson(route('social.users.all'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'username',
                    'last_login_at',
                    'status',
                ]
            ]
        ]);
    }

    /**
     * User Create.
     */
    public function testUserCreate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $this->withoutExceptionHandling();
        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $role = Role::create(['name' => 'Writer',]);

        $role->permissions()->sync([1, 2, 3]);


        $response = $this->actingAs($user)->postJson('/api/v1/user', [
            'department_id' => 1,
            'username' => "test_user",
            'name' => "Test User",
            'email' => "testuser@mail.com",
            'password' => "password",
            'password_confirmation' => 'password',
            'role' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "name",
                "username",
                "email",
                "updated_at",
                "created_at",
                "id",
                "roles"
            ]
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => true
        ]);
    }

    /**
     * User Update.
     */
    public function testUserUpdate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $role = Role::create(['name' => 'Writer',]);

        $role->permissions()->sync([1, 2, 3]);


        $response = $this->actingAs($user)->putJson('/api/v1/user/1', [

            'username' => "test_user",
            'name' => "Test User",
            'password' => "123456789",
            'password_confirmation' => "123456789",
            'role' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "id",
                "type",
                "name",
                "username",
                "email",
                "email_verified_at",
                "active",
                "last_login_ip",
                "timezone",
                "created_at",
                "last_login_at",
                "role",
            ]
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => true
        ]);
    }

    /**
     * User Delete single or multiple
     */
    public function testUserDelete(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly()->assignRole(Role::first());


        $response = $this->actingAs($user)->DeleteJson(route('admin.delete.user'),
        [
            'ids' => [1,2]
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "status",
            "message",
        ]);
    }

    public function testThatUserCanSearchOnUsername(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $users = User::factory(3)->createQuietly();

        $role = Role::create(['name' => 'Admin']);
        $role->permissions()->sync([1, 2, 3]);

        $searchAbleString = $users->first()->username;
        $response = $this->actingAs($user)
            ->getJson("/api/v1/user?username={$searchAbleString}");

       $response->assertSeeInOrder([$searchAbleString]);

       $response->assertDontSee(
            $users->filter(fn ($user) => $user->username !== $searchAbleString)
                ->pluck('username')
                ->toArray()
        );

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "data" => [
                '*' => [
                    'id',
                    'name',
                    'username',
                    'type',
                    'email',
                    'last_login_ip',
                    'join_date',
                    'active',
                    'created_at',
                    'role' => [
                        'id',
                        'name',
                        'created_at',
                    ]
                ]
            ],
            'meta' => [

            ],
            'links' => [

            ],
        ]);
    }
}
