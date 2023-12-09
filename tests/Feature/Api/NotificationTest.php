<?php

namespace Tests\Feature\Api;

use App\Models\User;
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
