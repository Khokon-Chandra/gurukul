<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Tests\FeatureBaseCase;

class NotificationTest extends FeatureBaseCase
{
    public function testCashflowList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

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
}
