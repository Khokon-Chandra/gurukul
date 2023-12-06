<?php

namespace Tests\Feature\Ip;

use App\Models\User;
use App\Models\UserIp;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        UserIp::create([
            'ip_address' => '103.15.245.75',
            'description' => 'testing Ip',
            'whitelisted' => 1,
            'created_by' => 2,
        ]);

        $user->assignRole(Role::where('name', 'Administrator')->first());


        $response = $this->actingAs($user)->getJson('/api/v1/user-ip');


        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip1',
                    'ip2',
                    'ip3',
                    'ip4',
                    'ip_address',
                    'whitelisted',
                    'description',
                    'created_at',
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
                    'ip_address' => '103.15.245.75',
                ],
                [
                    'ip_address' => '109.15.245.75',
                ],
                [
                    'ip_address' => '107.15.245.75',
                ],
            ])
            ->create();

        $user->assignRole(Role::where('name', 'Administrator')->first());

        $response = $this->actingAs($user)
            ->getJson(route('admin.user-ip.index', ['search' => '103']));

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
                    'ip_address',
                    'whitelisted',
                    'description',
                    'created_at',
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
            ->postJson('/api/v1/user-ip', [
                'number1' => 103,
                'number2' => 15,
                'number3' => 245,
                'number4' => 75,
                'description' => 'testing description',
            ]);


        $response->assertStatus(200);

        $this->assertDatabaseHas('user_ips', [
            'ip_address' => '103.15.245.75',
            'whitelisted' => 1,
        ]);

        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "ip_address",
                "description",
                "created_by",
                "created_at",
                "id",
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

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $user->assignRole(Role::where('name', 'Administrator')->first());


        $userIp = UserIp::factory()->create();


        $response = $this->actingAs($user)->putJson('/api/v1/user-ip/' . $userIp->id . '', [
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
                "id",
                "ip_address",
                "description",
                "whitelisted",
                "created_by",
                "updated_by",
                "deleted_by",
                "deleted_at",
                "created_at",
                "updated_at",
            ]
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => true
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
    public function test_userIpDelete(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $user->assignRole(Role::where('name', 'Administrator')->first());

        UserIp::create([
            'ip_address' => '103.15.245.74',
            'whitelisted' => 1,
            'description' => 'testing ip update',
            'created_by' => 1,
            'created_at' => now(),
        ]);

        UserIp::create([
            'ip_address' => '103.15.245.75',
            'whitelisted' => 1,
            'description' => 'testing ip update',
            'created_by' => 1,
            'created_at' => now(),
        ]);


        $response = $this->actingAs($user)->DeleteJson('/api/v1/user-ip/1,2');

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

        $data = UserIp::factory(3)
            ->sequence(...[
                [
                    'id' => 1,
                    'ip_address' => '103.15.245.75',
                ],
                [
                    'id' => 2,
                    'ip_address' => '109.15.245.75',
                ],
                [
                    'id' => 3,
                    'ip_address' => '107.15.245.75',
                ],
            ])
            ->create();


        $response = $this->actingAs($user)
            ->getJson(route('admin.user-ip.index', ['ip_address' => 'asc']));

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
                    'ip_address',
                    'whitelisted',
                    'description',
                    'created_at',
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
                    'ip_address' => '103.15.245.75',
                ],
                [
                    'id' => 2,
                    'description' => 'emeka',
                    'ip_address' => '109.15.245.75',
                ],
                [
                    'id' => 3,
                    'description' => 'favour',
                    'ip_address' => '107.15.245.75',
                ],
            ])
            ->create();


        $response = $this->actingAs($user)
            ->getJson(route('admin.user-ip.index', ['description' => 'asc']));

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
                    'ip_address',
                    'whitelisted',
                    'description',
                    'created_at',
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
                    'ip_address' => '103.15.245.75',
                ],
                [
                    'id' => 2,
                    'whitelisted' => false,
                    'ip_address' => '109.15.245.75',
                ],
                [
                    'id' => 3,
                    'whitelisted' => true,
                    'ip_address' => '107.15.245.75',
                ],
            ])
            ->create();


        $response = $this->actingAs($user)
            ->getJson(route('admin.user-ip.index', ['whitelisted' => 'desc']));

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
                    'ip_address',
                    'whitelisted',
                    'description',
                    'created_at',
                ]
            ],
            'links',
            'meta'
        ]);
    }

    public function testThatUserCanSortOnUpdatedAtField(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()->create()->assignRole(Role::first());

        $datas = UserIp::factory(3)
            ->sequence(...[
                [
                    'id' => 1,
                    'updated_at' => '2023-12-06 01:24:18',
                    'ip_address' => '103.15.245.75',
                ],
                [
                    'id' => 2,
                    'updated_at' => '2023-12-05 01:24:18',
                    'ip_address' => '109.15.245.75',
                ],
                [
                    'id' => 3,
                    'updated_at' => '2023-12-04 01:24:18',
                    'ip_address' => '107.15.245.75',
                ],
            ])
            ->create();


        $response = $this->actingAs($user)
            ->getJson(route('admin.user-ip.index', ['updated_at' => 'asc']));

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
                    'ip_address',
                    'whitelisted',
                    'description',
                    'created_at',
                ]
            ],
            'links',
            'meta'
        ]);
    }

}
