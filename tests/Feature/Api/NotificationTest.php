<?php

namespace Tests\Feature\Api;

use App\Models\Notification;
use App\Models\User;
use Tests\FeatureBaseCase;

class NotificationTest extends FeatureBaseCase
{
    public function testNotificationList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->getJson(route('service.notifications.index'));

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


    public function testStoreNotification()
    {
        $this->artisan('migrate:fresh --seed');

        $user     = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->postJson(route('service.notifications.store'), [
            'name'    => 'name of notification',
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

    public function testUpdateNotification()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username','administrator')->first();

        $notification = Notification::factory()->createQuietly();

        $response = $this->actingAs($user)->putJson(route('service.notifications.update', $notification->id), [
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



    public function testUpdateMultipleNotification()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username','administrator')->first();


        $response = $this->actingAs($user)->patchJson(route('service.notifications.updateMultiple'), [
            "notifications" => [
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


    public function testDestroyNotification()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->deleteJson(route('service.notifications.destroy',1));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
        ]);

    }


    public function testDeleteMultipleNotification()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->deleteJson(route('service.notifications.delete_multiple'),[
            'notifications' => [
                1,2,3,4,5
            ]
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
        ]);

    }



    public static function notificationData()
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
                    'name'    => 'Notification name',
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
