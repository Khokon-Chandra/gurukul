<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\FeatureBaseCase;

class DepartmentTest extends FeatureBaseCase
{
    /**
     * A basic feature test example.
     */
    public function testDepartmentList(): void
    {
        $this->artisan("migrate:fresh --seed");

        $user     = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->getJson(route('service.departments.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'description',
                    'menu',
                    'route',
                    'created_at',
                ]
            ]
        ]);
    }
}
