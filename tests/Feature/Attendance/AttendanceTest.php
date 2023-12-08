<?php

namespace Tests\Feature\Attendance;

use App\Models\Announcement;
use App\Models\Attendance;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Tests\FeatureBaseCase;
use Tests\TestCase;

class AttendanceTest extends FeatureBaseCase
{

    public function testThatUnauthorizedUserCannotCreateAttendance(): void
    {
        $data = [
            'username' => 'sney',
            'amount' => '20000'
        ];
        $response = $this->postJson(route('admin.attendance.create'), $data);
        $response->assertStatus(401);
    }

    public function testThatOnlyAuthorizedUserCanCreateAttendance(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        $data = [
            'username' => 'sney',
            'amount' => '20000'
        ];

        $response = $this->actingAs($user)->postJson(route('admin.attendance.create'), $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('attendances', [
            'username' => $data['username'],
            'amount' => $data['amount'],
        ]);

        $lastSavedAttendance = Attendance::orderBy('id', 'desc')->first();
        $this->assertEquals($lastSavedAttendance->username, $data['username']);
        $this->assertEquals($lastSavedAttendance->amount, $data['amount']);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'username',
                'amount',
                'created_at',
                'updated_at',
                'created_by' => [
                    'id',
                    'name',
                    'username',
                    'email'
                ]
            ]
        ]);
    }

    public function testThatUnauthorizedUserCannotDeleteAttendance(): void
    {
        $data = [1, 2, 3, 4, 5];
        $response = $this->deleteJson(route('admin.attendance.delete', $data));
        $response->assertStatus(401);
    }

    public function testThatOnlyAuthorizedUsersCanDeleteMultipleAttendance(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        Attendance::factory(4)->sequence(...[
            [
                'id' => 31,
                'username' => "sney",
                "amount" => 200
            ],
            [
                'id' => 34,
                'username' => "emeka",
                "amount" => 100
            ],
            [
                'id' => 35,
                'username' => "gift",
                "amount" => 120
            ],
            [
                'id' => 45,
                'username' => "gift",
                "amount" => 120
            ]
        ])->createQuietly();


        $data = [
            'attendances' =>  [31,34,35, 45]
        ];


        $this->assertDatabaseCount('attendances', 34);


        $response = $this->actingAs($user)->deleteJson(route('admin.attendance.delete'), $data);
        $response->assertStatus(200);



       //find all the deleted stuff and asset that their deleted at field is not null
        $deletedAttendances = Attendance::whereIn('id', [31,34,35,45])->get();
        foreach ($deletedAttendances as $attendance){
            $this->assertSoftDeleted($attendance);
        }


        $response->assertJsonStructure([
            'status',
            'message'
        ]);

    }

    public function testThatOnlyAuthorizedUsersCanDeleteASingleAttendance(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        $attendance = Attendance::factory()->create([
            'id' => 35,
            'username' => 'sney'
        ]);



        $data = [
            'attendances' =>  [35]
        ];

        $this->assertDatabaseCount('attendances', 31);

        $response = $this->actingAs($user)->deleteJson(route('admin.attendance.delete'), $data);
        $response->assertStatus(200);


        $deletedAttendance = Attendance::find(35);

        $this->assertSoftDeleted($deletedAttendance);


        $response->assertJsonStructure([
            'status',
            'message'
        ]);

    }


    public function testThatUnauthorizedUsersCannotUpdateAttendance(): void
    {
        $data = [1];
        $response = $this->putJson(route('admin.attendance.update', $data));
        $response->assertStatus(401);
    }
    public function testThatOnlyAuthorizedUserCanUpdateAttendance(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

         Attendance::factory()
            ->state([
                'id' => 35,
                'username' => 'gift',
                'amount' => 300
            ])
            ->createQuietly();


        $data = [
            'attendance' => [
                'id' => 35,
                'username' => 'Garnacho',
                'amount' => 2300
            ]

        ];

        $response = $this->actingAs($user)->putJson(route('admin.attendance.update'), $data);
        $response->assertStatus(200);

        $UpdatedAttendance = Attendance::find(35);
        $this->assertEquals($data['attendance']['username'] ,$UpdatedAttendance->username);
        $this->assertEquals($data['attendance']['amount'], $UpdatedAttendance->amount);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'username',
                'amount',
                'created_at',
                'updated_at',

            ]
        ]);
    }

    public function testThatUnauthorizedUsersCannotSeeAttendanceList(): void
    {

        $response = $this->getJson(route('admin.attendance.list'));
        $response->assertStatus(401);
    }

    public function testThatOnlyAuthorizedUsersCanSeeAttendanceList(): void
    {

        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        $response = $this->actingAs($user)->getJson(route('admin.attendance.list'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'username',
                        'amount',
                        'created_at',
                        'updated_at',
                        'created_by'
                    ]
                ],
                'links',
                'meta'
            ]

        ]);
    }

    public function testThatUserCanSearchOnUsernameField(): void
    {

        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        Attendance::factory(3)->sequence(...[
            [
                'id' => 31,
                'username' => "greenwood",
                "amount" => 200
            ],
            [
                'id' => 34,
                'username' => "emeka",
                "amount" => 100
            ],
            [
                'id' => 35,
                'username' => "gift",
                "amount" => 120
            ]
        ])->createQuietly();


        $response = $this->actingAs($user)->getJson(route('admin.attendance.list', ['search' => 'greenwood']));
        $response->assertStatus(200);



        $response->assertSeeInOrder(['greenwood']);

        $response->assertDontSee(['emeka', 'gift']);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'username',
                        'amount',
                        'created_at',
                        'updated_at',
                        'created_by'
                    ]
                ],
                'links',
                'meta'
            ]

        ]);
    }

    public function testThatUserCanSortOnUsernameField(): void {

        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        Attendance::factory(3)->sequence(...[
            [
                'id' => 31,
                'username' => "greenwood",
                "amount" => 200
            ],
            [
                'id' => 34,
                'username' => "emeka",
                "amount" => 100
            ],
            [
                'id' => 35,
                'username' => "gift",
                "amount" => 120
            ]
        ])->createQuietly();


        $response = $this->actingAs($user)->getJson(route('admin.attendance.list', ['username' => 'asc']));
        $response->assertStatus(200);



        $response->assertSeeInOrder(['emeka', 'gift', 'greenwood']);



        $response->assertJsonStructure([
            'status',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'username',
                        'amount',
                        'created_at',
                        'updated_at',
                        'created_by'
                    ]
                ],
                'links',
                'meta'
            ]

        ]);
    }

    public function testThatUserCanSortOnAmountField(): void {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        Attendance::factory(3)->sequence(...[
            [
                'id' => 31,
                'username' => "greenwood",
                "amount" => 3000000
            ],
            [
                'id' => 34,
                'username' => "emeka",
                "amount" => 2000000
            ],
            [
                'id' => 35,
                'username' => "gift",
                "amount" => 1000000
            ]
        ])->createQuietly();


        $response = $this->actingAs($user)->getJson(route('admin.attendance.list', ['amount' => 'desc']));
        $response->assertStatus(200);



        $response->assertSeeInOrder([3000000, 2000000, 1000000]);


        $response->assertJsonStructure([
            'status',
            'data' => [
               'data' => [
                   '*' => [
                       'id',
                       'username',
                       'amount',
                       'created_at',
                       'updated_at',
                       'created_by'
                   ]
               ],
                'links',
                'meta'
            ]

        ]);
    }
}
