<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testAttendanceIndex(): void
    {
        $this->artisan('migrate:fresh --seed');
        $user = User::where('username', 'administrator')->first();
        $response = $this->actingAs($user)->getJson(route('admin.attendances.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'id',
                    'username',
                    'clock',
                    'date',
                    'created_at',
                    'updated_at',
                    'created_by' => []
                ]
            ]
        ]);
    }
}
