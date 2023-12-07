<?php

namespace Tests\Feature\Attendance;

use App\Models\Attendance;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceTest extends TestCase
{

    public function testThatUnauthorizedUserCannotCreateAttendance(): void
    {
        $data = [
            'username' => 'sney',
            'amount' => '20000'
        ];
        $response = $this->postJson(route('admin.attendance.create'), $data);
        $response->assertStatus(401);
    }

    public function testThatOnlyAuthorizedUserCanCreateAttendance(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        $data = [
            'username' => 'sney',
            'amount' => '20000'
        ];

        $response = $this->actingAs($user)->postJson(route('admin.attendance.create'), $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('attendances', [
            'username' => $data['username'],
            'amount' => $data['amount'],
        ]);

        $lastSavedAttendance = Attendance::orderBy('id', 'desc')->first();
        $this->assertEquals($lastSavedAttendance->username, $data['username']);
        $this->assertEquals($lastSavedAttendance->amount, $data['amount']);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'username',
                'amount',
                'created_at',
                'updated_at',
                'created_by' => [
                    'id',
                    'name',
                    'username',
                    'email'
                ]
            ]
        ]);
    }

}
