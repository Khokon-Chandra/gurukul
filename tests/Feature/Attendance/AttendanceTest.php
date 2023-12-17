<?php

namespace Tests\Feature\Attendance;

use App\Models\Attendance;
use App\Models\User;
use Tests\FeatureBaseCase;

class AttendanceTest extends FeatureBaseCase
{
    public function testAttendanceList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->getJson(route('admin.attendances.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'amount',
                    'date',
                    'created_by' => [],
                ]
            ],
            'links',
            'meta',
        ]);
    }


    public function testStoreAttendance()
    {
        $this->artisan('migrate:fresh --seed');

        $user     = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->postJson(route('admin.attendances.store'), [
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
                'created_by' => [],
            ]
        ]);
    }








    /**
     * @test
     *
     * @dataProvider attendanceData
     */
    public function testAttendanceInputValidation($credentials, $errors, $errorKeys)
    {
        $this->artisan('migrate:fresh --seed');

        $user     = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->postJson(route('admin.attendances.store'), $credentials);

        $response->assertJsonValidationErrors($errorKeys);

        foreach ($errorKeys as $errorKey) {
            $response->assertJsonValidationErrorFor($errorKey);
        }

        $response->assertStatus(422);
    }

    public function testUpdateAttendance()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username', 'administrator')->first();

        $attendance = Attendance::factory()->createQuietly();

        $response = $this->actingAs($user)->putJson(route('admin.attendances.update', $attendance->id), [
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
                'created_by' => [],
            ]
        ]);
    }



    public function testUpdateMultipleAttendance()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username', 'administrator')->first();


        $response = $this->actingAs($user)->patchJson(route('admin.attendances.update_multiple'), [
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
                    'created_by' => [],
                ]
            ]
        ]);
    }


    public function testDestroyAttendance()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->deleteJson(route('admin.attendances.destroy', 1));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
        ]);
    }


    public function testDeleteMultipleAttendance()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->deleteJson(route('admin.attendances.delete_multiple'), [
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



    public static function attendanceData()
    {
        return [
            [
                [
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

        ];
    }
}
