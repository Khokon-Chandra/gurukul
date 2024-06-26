<?php

namespace Tests\Feature\User;

use App\Enum\UserTypeEnum;
use App\Models\User;
use App\Models\Role;
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


        $role = Role::create(['name' => 'Admin','department_id'=>$user->department_id]);
        $role->permissions()->sync([1, 2, 3]);
        
        $user->assignRole($role);

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
            'meta' => [],
            'links' => [],
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
            ->createQuietly()->assignRole(Role::first());


        $response = $this->actingAs($user)->postJson(route('users.user.store'), [
            'department_id' => 1,
            'username' => "test_user",
            'name' => "Test User",
            'email' => "testuser@mail.com",
            'password' => "password",
            'password_confirmation' => 'password',
            'role' => 1,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('users', 114);
        $this->assertDatabaseHas('users', [
            'department_id' => 1,
            'username' => "test_user",
            'name' => "Test User",
            'email' => "testuser@mail.com",
        ]);
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


        $response = $this->actingAs($user)->putJson(route('users.update.user', ['user' =>  2]), [
            'username' => "test_user",
            'name'     => "Test User Updated",
            'email'    => "info123@example.com",
            'password' => "123456789",
            'password_confirmation' => "123456789",
            'role'     => 1,
            'type'     => UserTypeEnum::MANAGEMENT,
        ]);

        $response->assertStatus(200);

        $UpdatedUser = User::find(2);

        $this->assertEquals('test_user',  $UpdatedUser->username);
        $this->assertEquals('Test User Updated',  $UpdatedUser->name);

        $response->assertJsonStructure([
            "status",
            "message"
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


        $response = $this->actingAs($user)->DeleteJson(
            route('users.delete.user'),
            [
                'ids' => [1, 2]
            ]
        );


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
            ->createQuietly()->assignRole(Role::first());

        $users = User::factory(3)->create();

        $searchableString = $users->first()->username;

        $response = $this->actingAs($user)->getJson(route('users.user.index', ['username' => $searchableString]));
        $response->assertStatus(200);
        $response->assertSeeInOrder([$searchableString]);
        $response->assertDontSee(
            $users->filter(fn ($user) => $user->username !== $searchableString)
                ->pluck('username')->toArray()
        );


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
                    'role' => []
                ]
            ],
            'meta' => [],
            'links' => [],
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

        $users = User::all();

        $response = $this->actingAs($user)->getJson('/api/v1/user?sort_name=desc');

        $response->assertStatus(200);


        $descPattern = '/^[zyxwvutsrqponmlkjihgfedcbaZYXWVUTSRQPONMLKJIHGFEDCBA]+/';


        $userFirstName = $users->first()->name;

        $firstNameParts = explode(' ',    $userFirstName);

        $firstName =   $firstNameParts[0];

        $this->assertMatchesRegularExpression($descPattern,   $firstName);


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
                    'role' => []
                ]
            ],
            'meta' => [],
            'links' => [],
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

        $users = User::all();
        $response = $this->actingAs($user)->getJson('/api/v1/user?sort_username=desc');
        $response->assertStatus(200);

        $descPattern = '/^[zyxwvutsrqponmlkjihgfedcbaZYXWVUTSRQPONMLKJIHGFEDCBA]+/';

        $firstUserName = $users->first()->username;

        $this->assertMatchesRegularExpression($descPattern,   $firstUserName);

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
                    'role' => []
                ]
            ],
            'meta' => [],
            'links' => [],
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

        $response = $this->actingAs($user)->getJson('/api/v1/user?sort_joindate=desc');
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
                    'role' => []
                ]
            ],
            'meta' => [],
            'links' => [],
        ]);
    }

    public function testThatUserCanSortUserRole(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory([])->state(['active' => true])
            ->createQuietly()->assignRole(Role::first());

        $userWithRole = User::factory([])->state(['active' => true])
            ->createQuietly()->assignRole(Role::first());


        $usersWithoutRole = User::factory(3)->create();


        $response = $this->actingAs($user)->getJson('/api/v1/user?sort_role=desc');

        $response->assertStatus(200);
        $response->assertSee([$userWithRole->username, $user->username]);
        $response->assertDontSee(
            $usersWithoutRole->filter(fn ($user) => $user->username !== null)
                ->pluck('username')->toArray()
        );


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
                    'role' => []
                ]
            ],
            'meta' => [],
            'links' => [],
        ]);
    }
}
