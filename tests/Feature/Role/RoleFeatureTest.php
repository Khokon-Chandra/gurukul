<?php

namespace Tests\Feature\Role;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Testing\Fluent\AssertableJson;


class RoleFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testUserRoleCreation(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->postJson(route('users.roles.store'), [
            'name' => Str::random(10),
            'department_id' => 1,
            'permissions' => [1, 2, 3]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "guard_name",
                "department_id",
                "name",
                "updated_at",
                "created_at",
                "id",
            ]
        ]);
    }


    /**
     * Update Role
     */
    public function testUserRoleUpdate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $role = Role::create([
            'name' => 'Test_Role',
            'department_id' => 1
        ]);


        $response = $this->actingAs($user)->putJson(route('users.roles.update', $role->id), [
            'name' => Str::random(10),
            'department_id' => 1,
            'permissions' => [1, 2, 3]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "id",
                "name",
                'users_count',
                'department',
                'permissions',
                "updated_at",
                "created_at",
            ]
        ]);
    }

    /**
     * Delete Role
     */

    public function testUserRoleDelete(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $role = Role::create([
            'name' => 'Test_Role',
            'department_id' => 1
        ]);


        $response = $this->actingAs($user)->deleteJson(route('users.roles.destroy', $role->id));


        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data"
        ]);
    }

    /**
     * Role List
     */
    public function testUserRoleList(): void
    {
        $this->artisan('migrate:fresh --seed');


        $user = User::where('username', 'administrator')->first();

        $role = Role::create(['name' => 'Admin', 'department_id' => 1]);

        $role->permissions()->sync([1, 2, 3]);


        $response = $this->actingAs($user)->getJson(route('users.roles.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'users_count',
                    'permissions',
                    'created_at',
                    'updated_at'
                ]
            ],
        ]);
    }

    /**
     * @test
     *
     * @dataProvider roleData
     */
    public function testRoleInputValidation($credentials, $errors, $errorKeys)
    {
        Notification::fake();

        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->postJson(route('users.roles.store'), $credentials);

        $response->assertJsonValidationErrors($errorKeys);

        foreach ($errorKeys as $errorKey) {
            $response->assertJsonValidationErrorFor($errorKey);
        }

        $response->assertStatus(422);
    }

    public static function roleData()
    {
        return [
            [
                [
                    "name" => "",
                    "permissions" => [1, 2, 3]
                ],
                [
                    "name" => "The name field is required."
                ],
                [
                    "name"
                ]
            ],
            [
                [
                    "name" => "Moderator",
                    "permissions" => '1,2,3',
                ],
                [
                    "permissions" => "The permissions field must be an array."
                ],
                [
                    "permissions"
                ]
            ]
        ];
    }
}
