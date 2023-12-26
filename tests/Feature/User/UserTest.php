<?php

namespace Tests\Feature\User;

use App\Models\User;
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
                    'email',
                    'last_login_ip',
                    'join_date',
                    'active',
                    'created_at',
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
            ->createQuietly()->assignRole(Role::first());


        $userToUpdate = User::factory()->create([
            'id' => 200
        ]);


        $response = $this->actingAs($user)->putJson(route('admin.update.user', ['user' =>  $userToUpdate->id]), [
            'username' => "test_user",
            'name' => "Test User Updated",
            'password' => "123456789",
            'password_confirmation' => "123456789",
            'role' => 1,
        ]);

        $response->assertStatus(200);

        $UpdatedUser = User::find($userToUpdate->id);

        $this->assertEquals('test_user',  $UpdatedUser->username);
        $this->assertEquals('Test User Updated',  $UpdatedUser->name);

        $response->assertJsonStructure([
            "status",
            "message",

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
            ->createQuietly();

        $role = Role::create(['name' => 'Writer',]);
        $role->permissions()->sync([1, 2, 3]);

        User::create([
            'department_id'=>1,
            'username' => "test_user1",
            'name' => "Test Use1r",
            'email' => "testuser1@mail.com",
            'password' => 'password',
            'roles' => [1],
        ]);

        User::create([
            'department_id'=>1,
            'username' => "test_user2",
            'name' => "Test User2",
            'email' => "testuser2@mail.com",
            'password' => 'password',
            'roles' => [1],
        ]);


        $response = $this->actingAs($user)->DeleteJson('/api/v1/user/1,2');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data",
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => false
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

    $users = User::factory(3)
            ->sequence(...[
                [
                    'id' => 200,
                    'username' => "James",
                ],
                [
                    'id' => 201,
                    'username' => "John",
                ],
                [
                    'id' => 202,
                    'username' => "Peter",
                ],
            ])->createQuietly();

        $role = Role::create(['name' => 'Admin']);
        $role->permissions()->sync([1, 2, 3]);



        $response = $this->actingAs($user)->getJson('/api/v1/user?username=James');

       $response->assertSeeInOrder(['James']);
        $response->assertDontSee(['John', 'Peter']);


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
