<?php

namespace Tests\Feature\Attendance;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Tests\FeatureBaseCase;

class AttendanceTest extends FeatureBaseCase
{
    public function testAttendanceList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->getJson(route('users.attendances.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'amount',
                    'date',
                    'department',
                    'created_by' => [],
                ]
            ],
            'links',
            'meta',
        ]);
    }


    public function testStoreAttendance(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user     = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->postJson(route('users.attendances.store'), [
            'department_id' => 1,
            'name'    => 'name of attendance',
            'amount'  => 20000.1003,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'name',
                'amount',
                'date',
                'department',
                'created_by' => [],
            ]
        ]);
    }








    /**
     * @test
     *
     * @dataProvider attendanceData
     */
    public function testAttendanceInputValidation($credentials, $errors, $errorKeys): void
    {
        $this->artisan('migrate:fresh --seed');

        $user     = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->postJson(route('users.attendances.store'), $credentials);

        $response->assertJsonValidationErrors($errorKeys);

        foreach ($errorKeys as $errorKey) {
            $response->assertJsonValidationErrorFor($errorKey);
        }

        $response->assertStatus(422);
    }

    public function testUpdateAttendance(): void
    {
        $this->artisan('migrate:fresh --seed');

        Notification::fake();

        $user = User::where('username', 'administrator')->first();

        $attendance = Attendance::factory()->createQuietly();

        $response = $this->actingAs($user)->putJson(route('users.attendances.update', $attendance->id), [
            'name' => 'Dummy text for update',
            'amount'    => 20000,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'name',
                'amount',
                'date',
                'department',
                'created_by' => [],
            ]
        ]);
    }



    public function testUpdateMultipleAttendance(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->patchJson(route('users.attendances.update_multiple'), [
            "attendances" => [
                [
                    'id' => 1,
                    'name' => 'update 1',
                    'amount' => 10000,
                ],
                [
                    'id' => 2,
                    'name' => 'update 2',
                    'amount' => 20000,
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'amount',
                    'date',
                    'department',
                    'created_by' => [],
                ]
            ]
        ]);
    }


    public function testDestroyAttendance(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->deleteJson(route('users.attendances.destroy', 1));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
        ]);
    }


    public function testDeleteMultipleAttendance(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->deleteJson(route('users.attendances.delete_multiple'), [
            'attendances' => [
                1, 2, 3, 4, 5
            ]
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
        ]);
    }



    public static function attendanceData(): array
    {
        return [
            [
                [
                    'department_id' => 1,
                    'amount'    => 10000,
                ],
                [
                    'name' => [
                        "The name field is required."
                    ]
                ],
                [
                    'name'
                ]
            ],
            [
                [
                    'department_id' => 1,
                    'name'    => 'Attendance name',
                ],
                [
                    'amount' => [
                        "The amount field is required."
                    ]
                ],
                [
                    'amount'
                ]
            ],

            [
                [
                    'name'    => 'Attendance name',
                    'amount'  => 300000
                ],
                [
                    'department_id' => [
                        "The department id field is required."
                    ]
                ],
                [
                    'department_id'
                ]
            ],

        ];
    }
}
