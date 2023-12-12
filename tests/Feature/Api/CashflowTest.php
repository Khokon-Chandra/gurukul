<?php

namespace Tests\Feature\Api;

use App\Models\Cashflow;
use App\Models\User;
use Tests\FeatureBaseCase;

class CashflowTest extends FeatureBaseCase
{
    public function testCashflowList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->getJson(route('service.cashflows.index'));

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


    public function testStoreCashflow()
    {
        $this->artisan('migrate:fresh --seed');

        $user     = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->postJson(route('service.cashflows.store'), [
            'name'    => 'name of cashflow',
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
     * @dataProvider cashflowData
     */
    public function testCashflowInputValidation($credentials, $errors, $errorKeys)
    {
        $this->artisan('migrate:fresh --seed');

        $user     = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->postJson(route('service.cashflows.store'), $credentials);

        $response->assertJsonValidationErrors($errorKeys);

        foreach ($errorKeys as $errorKey) {
            $response->assertJsonValidationErrorFor($errorKey);
        }

        $response->assertStatus(422);
    }

    public function testUpdateCashflow()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username','administrator')->first();

        $cashflow = Cashflow::factory()->createQuietly();

        $response = $this->actingAs($user)->putJson(route('service.cashflows.update', $cashflow->id), [
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



    public function testUpdateMultipleCashflow()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username','administrator')->first();


        $response = $this->actingAs($user)->patchJson(route('service.cashflows.update_multiple'), [
            "cashflows" => [
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


    public function testDestroyCashflow()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->deleteJson(route('service.cashflows.destroy',1));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
        ]);

    }


    public function testDeleteMultipleCashflow()
    {
        $this->artisan('migrate:fresh --seed');

        $user         = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->deleteJson(route('service.cashflows.delete_multiple'),[
            'cashflows' => [
                1,2,3,4,5
            ]
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
        ]);

    }



    public static function cashflowData()
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
                    'name'    => 'Cashflow name',
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
