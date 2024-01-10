<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\FeatureBaseCase;

class DashboardTest extends FeatureBaseCase
{
    /**
     * A basic feature test example.
     */
    public function testDashboardIndex(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->getJson('api/v1/dashboard');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'department_name',
                    'more_users',
                    'users_preview',
                ]
            ]
        ]);

    }
}
