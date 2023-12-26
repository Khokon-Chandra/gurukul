<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\FeatureBaseCase;

class UserLoginTest extends FeatureBaseCase
{
    public function testUserCanSuccessfullyLogin()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        $response = $this->postJson('/api/v1/login', [
            'username' => $user->username,
            'password' => 'password',
        ], $this->headers);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'username',
                    'email',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'department_id',
                ],
            ]
        ]);

        $response->assertJson([
            'status' => 'success',
            'data' => [
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * @test
     *
     * @dataProvider userLoginData
     */
    public function testUserLoginInputValidation($credentials, $errors, $errorKeys)
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        $response = $this->postJson('/api/v1/login', $credentials);

        $response->assertJsonValidationErrors($errorKeys);
        foreach ($errorKeys as $errorKey) {
            $response->assertJsonValidationErrorFor($errorKey);
        }

        $inValidPasswordResponse = $this->postJson('/api/v1/login', [
            'username' => $user->username,
            'password' => 'someNotCorrectPassword'
        ], $this->headers);

        $inValidPasswordResponse->assertJson([
            "status" => "error",
            "message" => "Invalid Login Credentials"
        ]);
    }

    /**
     * @test
     *
     *
     */
    public function testDeactivatedUserCannotLogin()
    {
        $this->artisan("migrate:fresh --seed");

        $user = User::factory()
            ->sequence([
                'active' => false
            ])
            ->createQuietly();

        $response = $this->postJson('/api/v1/login', [
            'username' => $user->username,
            'password' => 'password',
        ], $this->headers);

        $response->assertStatus(400);

        $response->assertJsonStructure([
            'status',
            'message',
        ]);

        $response->assertJson([
            'status' => 'error',
            'message' => "Username has been deactivate!."
        ]);
    }


    public function testUserHasPermission()
    {
        $this->artisan("migrate:fresh --seed");

        $user = User::factory()
            ->sequence([
                'active' => true
            ])
            ->createQuietly();

        $role = Role::where('name','Administrator')->first();

        $permissions = [
            [
                'name' => 'View',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Create',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Show',
                'guard_name' => 'web',
            ]
        ];

        $role->permissions()->createMany($permissions);

        $user->assignRole($role);
        $user->permissions()->sync($role->permissions->pluck('id')->toArray());

        $response = $this->postJson('/api/v1/login', [
            'username' => $user->username,
            'password' => 'password',
        ], $this->headers);

        $response->assertStatus(200);
    }



    public function testUserHasNoPermission()
    {
        $this->artisan("migrate:fresh --seed");

        $user = User::factory()
            ->sequence([
                'active' => true
            ])
            ->createQuietly();

        $response = $this->postJson('/api/v1/login', [
            'username' => $user->username,
            'password' => 'password',
        ], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'permissions' => [],
            ],
        ]);

        $response->assertJson([
            'data' => [
                'permissions' => [],
            ],
        ]);
    }



    public static function userLoginData(): array
    {
        return [
            [
                [
                    'username' => 'username',
                    'password' => 'password',
                ],
                [
                    'username' => [
                        'The selected username is invalid.',
                    ],
                ],
                [
                    'username'
                ]
            ],
            [
                [
                    'password' => 'password',
                ],
                [
                    'username' => [
                        'The username field is required.'
                    ],
                ],
                [
                    'username'
                ]
            ],
            [
                [
                    'username' => 'username',
                ],
                [
                    'password' => ['The password field is required.'],
                ],
                [
                    'password'
                ]
            ]
        ];
    }
}
