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


}
