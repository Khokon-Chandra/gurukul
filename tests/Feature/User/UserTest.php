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
            ->createQuietly();

        $role = Role::create(['name' => 'Writer',]);

        $role->permissions()->sync([1, 2, 3]);


        $response = $this->actingAs($user)->putJson('/api/v1/user/1', [
            'department_id' => 1,
            'username' => "test_user",
            'name' => "Test User",
            'email' => "testuser@mail.com",
            'role' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",

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
            ->createQuietly();

        $role = Role::create(['name' => 'Writer',]);
        $role->permissions()->sync([1, 2, 3]);

        User::create([
            'department_id' => 1,
            'username' => "test_user1",
            'name' => "Test Use1r",
            'email' => "testuser1@mail.com",
            'password' => 'password',
            'roles' => [1],
        ]);

        User::create([
            'department_id' => 1,
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
            ->createQuietly()->assignRole(Role::first());

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

        $response = $this->actingAs($user)->getJson(route('admin.user.index', ['username' => 'James']));
        $response->assertStatus(200);
        $response->assertSeeInOrder(['James']);
        $response->assertDontSee(['John', 'Peter']);


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

    public function testThatUserCanSortName(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly()->assignRole(Role::first());

        $users = User::factory(3)
            ->sequence(...[
                [
                    'id' => 200,
                    'username' => "James",
                    'name' => "funke"
                ],
                [
                    'id' => 201,
                    'username' => "John",
                    'name' => 'emeka'
                ],
                [
                    'id' => 202,
                    'username' => "Peter",
                    'name' => 'jeniffer'
                ],
            ])->createQuietly();

        $role = Role::create(['name' => 'Admin']);
        $role->permissions()->sync([1, 2, 3]);


        $response = $this->actingAs($user)->getJson('/api/v1/user?sort_name=asc');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['emeka', 'funke', 'jeniffer']);
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

    public function testThatUserCanSortUserName(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly()->assignRole(Role::first());

        $users = User::factory(3)
            ->sequence(...[
                [
                    'id' => 200,
                    'username' => "Abel",
                    'name' => "funke"
                ],
                [
                    'id' => 201,
                    'username' => "Cain",
                    'name' => 'emeka'
                ],
                [
                    'id' => 202,
                    'username' => "Bello",
                    'name' => 'jeniffer'
                ],
            ])->createQuietly();

        $role = Role::create(['name' => 'Admin']);
        $role->permissions()->sync([1, 2, 3]);


        $response = $this->actingAs($user)->getJson('/api/v1/user?sort_username=desc');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Cain', 'Bello', 'Abel']);
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

    public function testThatUserCanSortJoinDate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly()->assignRole(Role::first());

        $users = User::factory(3)
            ->sequence(...[
                [
                    'id' => 200,
                    'username' => "Abel",
                    'name' => "funke",
                    'created_at' => '2023-12-17'
                ],
                [
                    'id' => 201,
                    'username' => "Cain",
                    'name' => 'emeka',
                    'created_at' => '2023-12-18'
                ],
                [
                    'id' => 202,
                    'username' => "Bello",
                    'name' => 'jeniffer',
                    'created_at' => '2023-12-19'

                ],
            ])->createQuietly();

        $role = Role::create(['name' => 'Admin']);
        $role->permissions()->sync([1, 2, 3]);


        $response = $this->actingAs($user)->getJson('/api/v1/user?sort_joindate=desc');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Bello', 'Cain', 'Abel']);
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

    public function testThatUserCanSortUserRole(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory([
            'id' => 300,
            'username' => "sney",
            'name' => "sneymoney",
        ])
            ->state([
                'active' => true
            ])
            ->createQuietly()->assignRole(Role::first());

        $userWithRole = User::factory([
            'id' => 302,
            'username' => "emoney1",
            'name' => "emoney",
        ])
            ->state([
                'active' => true
            ])
            ->createQuietly()->assignRole(Role::first());


        $usersWithoutRole = User::factory(3)
            ->sequence(...[
                [
                    'id' => 200,
                    'username' => "Abel",
                    'name' => "funke",
                    'created_at' => '2023-12-17'
                ],
                [
                    'id' => 201,
                    'username' => "Cain",
                    'name' => 'emeka',
                    'created_at' => '2023-12-18'
                ],
                [
                    'id' => 202,
                    'username' => "Bello",
                    'name' => 'jeniffer',
                    'created_at' => '2023-12-19'

                ],
            ])->createQuietly();


        $response = $this->actingAs($user)->getJson('/api/v1/user?sort_role=desc');

        $response->assertStatus(200);
        $response->assertSee(['sneymoney', 'emoney']);
        $response->assertDontSeeText(['bello', 'funke', 'jeniffer']);


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
