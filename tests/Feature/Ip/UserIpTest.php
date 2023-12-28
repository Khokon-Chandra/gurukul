<?php

namespace Tests\Feature\Ip;

use App\Models\Department;
use App\Models\User;
use App\Models\UserIp;
use Spatie\Permission\Models\Role;
use Tests\FeatureBaseCase;

class UserIpTest extends FeatureBaseCase
{
    /**
     * User Ip List
     */
    public function testUserIpList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        UserIp::create([
            'department_id' => 1,
            'ip' => '103.15.245.75',
            'description' => 'testing Ip',
            'whitelisted' => 1,
            'created_by' => 2,
        ]);


        $response = $this->actingAs($user)->json('GET', '/api/v1/ip', ['department_id' => 1]);


        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip1',
                    'ip2',
                    'ip3',
                    'ip4',
                    'ip',
                    'department',
                    'description',
                    'status',
                    'date',
                ]
            ],
            'links',
            'meta'
        ]);
    }

    public function testThatUserCanSearchOnTheIpAddressField(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        UserIp::factory(3)
            ->sequence(...[
                [
                    'department_id' => 1,
                    'ip' => '103.15.245.75',
                ],
                [
                    'department_id' => 1,
                    'ip' => '109.15.245.75',
                ],
                [
                    'department_id' => 1,
                    'ip' => '107.15.245.75',
                ],
            ])
            ->create();

        $user->assignRole(Role::where('name', 'Administrator')->first());

        $response = $this->actingAs($user)
            ->getJson(route('users.ip.index', ['department_id' => 1, 'ip' => '103.15.245.75']));

        $response->assertStatus(200);


        $this->assertCount(3, UserIp::all());


        $response->assertSeeInOrder(['103.15.245.75']);



        $response->assertDontSee([
            '107.15.245.75',
            '109.15.245.75',
        ]);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip1',
                    'ip2',
                    'ip3',
                    'ip4',
                    'ip',
                    'department',
                    'description',
                    'status',
                    'date',
                ]
            ],
            'links',
            'meta'
        ]);
    }

    /**
     * User Ip Creation
     */
    public function testUserWithAppropriatePrivilegeCanIpCreate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $user->assignRole(Role::where('name', 'Administrator')->first());

        $response = $this->actingAs($user)
            ->postJson('/api/v1/ip', [
                'department_id' => 1,
                'number1' => 103,
                'number2' => 15,
                'number3' => 245,
                'number4' => 75,
                'description' => 'testing description',
            ]);


        $response->assertStatus(200);

        $this->assertDatabaseHas('user_ips', [
            'department_id' => 1,
            'ip' => '103.15.245.75',
            'whitelisted' => 1,
        ]);

        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                'id',
                'ip1',
                'ip2',
                'ip3',
                'ip4',
                'ip',
                'department',
                'description',
                'status',
                'date',
            ]
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => true
        ]);
    }

    /**
     * User Ip Update
     */
    public function testUserIpUpdate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user   = User::where('username', 'administrator')->first();

        $userIp = UserIp::factory()->create();

        $response = $this->actingAs($user)->putJson(route('users.ip.update',$userIp->id), [
            'department_id' => 1,
            'number1' => 103,
            'number2' => 15,
            'number3' => 245,
            'number4' => 75,
            'whitelisted' => 1,
            'description' => 'testing updated descriptoin',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                'id',
                'ip1',
                'ip2',
                'ip3',
                'ip4',
                'ip',
                'department',
                'description',
                'status',
                'date',
            ]
        ]);

        $response->assertJson([
            'status'  => true,
            'message' => true,
            'data'    => true
        ]);
    }

    /**
     * Users Ip Update Multiple
     */
    public function testUserIpUpdateMultiple(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $user->assignRole(Role::where('name', 'Administrator')->first());

        UserIp::factory(2)->create();

        $data = [
            "items" => [
                [
                    "id" => 1,
                    "item" => [
                        'department_id' => 1,
                        "number1" => "103",
                        "number2" => "15",
                        "number3" => "245",
                        "number4" => "80",
                        "whitelisted" => 1,
                        "description" => "testing Ip Updated"
                    ]
                ],
                [
                    "id" => 2,
                    "item" => [
                        'department_id' => 1,
                        "number1" => "103",
                        "number2" => "15",
                        "number3" => "245",
                        "number4" => "90",
                        "whitelisted" => 1,
                        "description" => "testing Ip Updated"
                    ]
                ]
            ]
        ];


        $response = $this->actingAs($user)->putJson('/api/v1/user-ips', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data"
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => true
        ]);
    }


    /**
     * User Ip Delete single or multiple
     */
    public function testUserIpDelete(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $user->assignRole(Role::where('name', 'Administrator')->first());

        $userIpData = UserIp::factory(2)
            ->create();

        $response = $this->actingAs($user)
            ->DeleteJson("/api/v1/ip/{$userIpData->first()->id}");

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

    public function testUserIpDeleteMultiple(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $user->assignRole(Role::where('name', 'Administrator')->first());

        $userIpData = UserIp::factory(2)
            ->create();

        $response = $this->actingAs($user)
            ->DeleteJson('/api/v1/user-ip-delete-multiple',[
            'items' => $userIpData->pluck('id')->toArray()
        ]);

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

    public function testThatUserCanSortOnIpAddressField(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()->create()->assignRole(Role::first());

        $department = Department::inRandomOrder()->first();

        $ips = UserIp::factory(3)
            ->state([
                'department_id' => $department->id
            ])
            ->create();

        $ipFilters = $ips->pluck('ip')
            ->sort()
            ->toArray();

        $response = $this->actingAs($user)
            ->getJson(route('users.ip.index', [
                'department_id' => $department->id,
                'sort_by' => 'ip',
                'sort_type' => 'ASC',
            ]));

        $response->assertStatus(200);

        $this->assertCount(3, UserIp::all());

        $response->assertSeeInOrder($ipFilters);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip1',
                    'ip2',
                    'ip3',
                    'ip4',
                    'ip',
                    'department',
                    'status',
                    'description',
                    'date',
                ]
            ],
            'links',
            'meta'
        ]);
    }

    public function testThatUserCanSortOnDescriptionField(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()->create()->assignRole(Role::first());

        $department = Department::inRandomOrder()->first();

        $userIpData = UserIp::factory(3)
            ->state([
                'department_id' => $department->id
            ])
            ->create();

        $response = $this->actingAs($user)
            ->getJson(route('users.ip.index', [
                'department_id' => $department->id,
                'sort_by' => 'description',
                'sort_type' => 'ASC',
            ]));

        $response->assertStatus(200);

        $this->assertCount(3, UserIp::all());

        $response->assertSeeInOrder($userIpData->pluck('description')->toArray());

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip1',
                    'ip2',
                    'ip3',
                    'ip4',
                    'ip',
                    'department',
                    'status',
                    'description',
                    'date',
                ]
            ],
            'links',
            'meta'
        ]);
    }

    public function testThatUserCanSortOnWhitelistedField(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()->create()->assignRole(Role::first());

        $department = Department::inRandomOrder()->first();

        $userIpData = UserIp::factory(3)
            ->state([
                'department_id' => $department->id,
            ])
            ->create();

        $response = $this->actingAs($user)
            ->getJson(route('users.ip.index', [
                'department_id' => $department->id,
                'sort_by' => 'status',
                'sort_type' => 'DESC'
            ]));

        $response->assertStatus(200);

        $this->assertCount(3, UserIp::all());

        $response->assertSeeInOrder($userIpData->pluck('whitelisted')->toArray());

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip1',
                    'ip2',
                    'ip3',
                    'ip4',
                    'ip',
                    'department',
                    'status',
                    'description',
                    'date',
                ]
            ],
            'links',
            'meta'
        ]);
    }

    public function testThatUserCanSortOnDateAtField(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()->create()->assignRole(Role::first());

        $department = Department::inRandomOrder()->first();

        $userIpData = UserIp::factory(3)
            ->state([
                'department_id' => $department->id,
            ])
            ->create();

        $response = $this->actingAs($user)
            ->getJson(route('users.ip.index', [
                'department_id' => $department->id,
                'sort_by'   => 'date',
                'sort_type' => 'ASC',
            ]));

        $response->assertStatus(200);

        $this->assertCount(3, UserIp::all());

        $response->assertSeeInOrder($userIpData->pluck('ip')->toArray());

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip1',
                    'ip2',
                    'ip3',
                    'ip4',
                    'ip',
                    'department',
                    'status',
                    'description',
                    'date',
                ]
            ],
            'links',
            'meta'
        ]);
    }
}
