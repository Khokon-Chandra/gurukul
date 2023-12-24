<?php

namespace Tests\Feature\Ip;

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
            'ip' => '103.15.245.75',
            'description' => 'testing Ip',
            'whitelisted' => 1,
            'created_by' => 2,
        ]);


        $response = $this->actingAs($user)->getJson('/api/v1/ip');


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
                    'ip' => '103.15.245.75',
                ],
                [
                    'ip' => '109.15.245.75',
                ],
                [
                    'ip' => '107.15.245.75',
                ],
            ])
            ->create();

        $user->assignRole(Role::where('name', 'Administrator')->first());

        $response = $this->actingAs($user)
            ->getJson(route('users.ip.index', ['ip' => '103.15.245.75']));

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
                'number1' => 103,
                'number2' => 15,
                'number3' => 245,
                'number4' => 75,
                'description' => 'testing description',
            ]);


        $response->assertStatus(200);

        $this->assertDatabaseHas('user_ips', [
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
    public function testUserIpUpdate()
    {
        $this->artisan('migrate:fresh --seed');

        $user   = User::where('username', 'administrator')->first();

        $userIp = UserIp::factory()->create();

        $response = $this->actingAs($user)->putJson(route('users.ip.update',$userIp->id), [
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
    public function testUserIpUpdateMultiple()
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

        UserIp::create([
            'ip' => '103.15.245.74',
            'whitelisted' => 1,
            'description' => 'testing ip update',
            'created_by' => 1,
            'created_at' => now(),
        ]);

        UserIp::create([
            'ip' => '103.15.245.75',
            'whitelisted' => 1,
            'description' => 'testing ip update',
            'created_by' => 1,
            'created_at' => now(),
        ]);


        $response = $this->actingAs($user)->DeleteJson('/api/v1/ip/1');

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

        UserIp::create([
            'ip' => '103.15.245.74',
            'whitelisted' => 1,
            'description' => 'testing ip update',
            'created_by' => 1,
            'created_at' => now(),
        ]);

        UserIp::create([
            'ip' => '103.15.245.75',
            'whitelisted' => 1,
            'description' => 'testing ip update',
            'created_by' => 1,
            'created_at' => now(),
        ]);


        $response = $this->actingAs($user)->DeleteJson('/api/v1/user-ip-delete-multiple',[
            'items' => [1,2]
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

        UserIp::factory(3)
            ->sequence(...[
                [
                    'id' => 1,
                    'ip' => '103.15.245.75',
                ],
                [
                    'id' => 2,
                    'ip' => '109.15.245.75',
                ],
                [
                    'id' => 3,
                    'ip' => '107.15.245.75',
                ],
            ])
            ->create();


        $response = $this->actingAs($user)
            ->getJson(route('users.ip.index', [
                'sort_by' => 'ip',
                'sort_type' => 'ASC',
            ]),);

        $response->assertStatus(200);


        $this->assertCount(3, UserIp::all());


        $response->assertSeeInOrder(['103.15.245.75',  '107.15.245.75', '109.15.245.75']);


        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip1',
                    'ip2',
                    'ip3',
                    'ip4',
                    'ip',
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

        $datas = UserIp::factory(3)
            ->sequence(...[
                [
                    'id' => 1,
                    'description' => 'demas',
                    'ip' => '103.15.245.75',
                ],
                [
                    'id' => 2,
                    'description' => 'emeka',
                    'ip' => '109.15.245.75',
                ],
                [
                    'id' => 3,
                    'description' => 'favour',
                    'ip' => '107.15.245.75',
                ],
            ])
            ->create();


        $response = $this->actingAs($user)
            ->getJson(route('users.ip.index', [
                'sort_by' => 'description',
                'sort_type' => 'ASC',
            ]));

        $response->assertStatus(200);


        $this->assertCount(3, UserIp::all());


        $response->assertSeeInOrder(['demas', 'emeka', 'favour']);


        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip1',
                    'ip2',
                    'ip3',
                    'ip4',
                    'ip',
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

        $datas = UserIp::factory(3)
            ->sequence(...[
                [
                    'id' => 1,
                    'whitelisted' => true,
                    'ip' => '103.15.245.75',
                ],
                [
                    'id' => 2,
                    'whitelisted' => false,
                    'ip' => '109.15.245.75',
                ],
                [
                    'id' => 3,
                    'whitelisted' => true,
                    'ip' => '107.15.245.75',
                ],
            ])
            ->create();


        $response = $this->actingAs($user)
            ->getJson(route('users.ip.index', [
                'sort_by' => 'status',
                'sort_type' => 'DESC'
            ]));

        $response->assertStatus(200);


        $this->assertCount(3, UserIp::all());


        $response->assertSeeInOrder(['false', 'false', 'true']);


        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip1',
                    'ip2',
                    'ip3',
                    'ip4',
                    'ip',
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

        $datas = UserIp::factory(3)
            ->sequence(...[
                [
                    'id' => 1,
                    'updated_at' => '2023-12-06 01:24:18',
                    'ip' => '103.15.245.75',
                ],
                [
                    'id' => 2,
                    'updated_at' => '2023-12-05 01:24:18',
                    'ip' => '109.15.245.75',
                ],
                [
                    'id' => 3,
                    'updated_at' => '2023-12-04 01:24:18',
                    'ip' => '107.15.245.75',
                ],
            ])
            ->create();


        $response = $this->actingAs($user)
            ->getJson(route('users.ip.index', [
                'sort_by'   => 'date',
                'sort_type' => 'ASC',
            ]));

        $response->assertStatus(200);


        $this->assertCount(3, UserIp::all());


        $response->assertSeeInOrder(['107.15.245.75', '109.15.245.75', '103.15.245.75']);


        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip1',
                    'ip2',
                    'ip3',
                    'ip4',
                    'ip',
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
