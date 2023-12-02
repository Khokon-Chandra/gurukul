<?php

namespace Tests\Feature\Api;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Tests\FeatureBaseCase;

class NotificationTest extends FeatureBaseCase
{
    public function testCashflowList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->getJson(route('service.notifications.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'subject',
                    'date',
                    'time',
                    'created_at',
                    'created_by' => [],
                ]
            ],
            'links',
            'meta',
        ]);
    }



    public function testStoreNotificationSuccessfully()
    {
        $this->artisan('migrate:fresh --seed');

        $user     = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->postJson(route('service.notifications.store'), [
            'subject' => 'Dummy text for subject',
            'date'    => '2023-10-16',
            'time'    => '02:01'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'subject',
                'date',
                'time',
                'created_by' => [],
                'created_at'
            ]
        ]);
    }


    public function testUpdateNotificationSuccessfully()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username','administrator')->first();

        $notification = Notification::factory()->createQuietly();

        $response = $this->actingAs($user)->putJson(route('service.notifications.update', $notification->id), [
            'subject' => 'Dummy text for update',
            'date'    => '2023-10-16',
            'time'    => '02:01'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'subject',
                'date',
                'time',
                'created_by' => [],
                'created_at'
            ]
        ]);
    }


    public function testDeleteMultipleNotificationById()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username','administrator')->first();

        $notifications = Notification::take(5)->pluck('id')->toArray();
        $notifications = implode(',',$notifications);

        $response = $this->actingAs($user)->deleteJson(route('service.notifications.destroy', $notifications));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message'
        ]);
    }


    /**
     * @test
     *
     * @dataProvider notificationData
     */
    public function testNotificationInputValidation($credentials, $errors, $errorKeys)
    {
        $this->artisan('migrate:fresh --seed');

        $user     = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->postJson(route('service.notifications.store'), $credentials);

        $response->assertJsonValidationErrors($errorKeys);

        foreach ($errorKeys as $errorKey) {
            $response->assertJsonValidationErrorFor($errorKey);
        }

        $response->assertStatus(422);
    }


    /**
     * @test
     *
     * @dataProvider notificationData
     */
    public function testNotificationUpdateValidation($credentials, $errors, $errorKeys)
    {
        $this->artisan('migrate:fresh --seed');

        $user     = User::where('username','administrator')->first();

        $notification = Notification::factory()->createQuietly();

        $response = $this->actingAs($user)->putJson(route('service.notifications.update', $notification->id), $credentials);

        $response->assertJsonValidationErrors($errorKeys);

        foreach ($errorKeys as $errorKey) {
            $response->assertJsonValidationErrorFor($errorKey);
        }

        $response->assertStatus(422);
    }


    public static function notificationData()
    {
        return [
            [
                [
                    'date'    => '2023-03-16',
                    'time'    => '02:01',
                ],
                [
                    'subject' => [
                        "The subject field is required."
                    ]
                ],
                [
                    'subject'
                ]
            ],
            [
                [
                    'subject'    => 'Notification subject',
                    'time'    => '02:01',
                ],
                [
                    'date' => [
                        "The date field is required."
                    ]
                ],
                [
                    'date'
                ]
            ],
            [
                [
                    'subject'    => 'Notification subject',
                    'date'    => '2023-10-01',
                ],
                [
                    'time' => [
                        "The time field is required."
                    ]
                ],
                [
                    'time'
                ]
            ]
        ];
    }
}
