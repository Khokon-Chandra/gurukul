<?php

namespace Tests\Feature\Activity;

use App\Models\Department;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Tests\FeatureBaseCase;

class ActivityLogTest extends FeatureBaseCase
{
    /**
     * A basic feature test example.
     */
    public function testUserCanSeeActivityLogList(): void
    {
        $this->artisan("migrate:fresh --seed");

        $this->artisan("db:seed --class=ActivityLogSeeder");

        $user     = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->getJson(route('users.activities.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'log_name',
                    'description',
                    'target',
                    'activity',
                    'ip',
                    'created_at',
                    'department'
                ],

            ],
            "meta" => [
                'current_page',
                'from',
                'links',
                'per_page',
                'to',
                'total',
            ]
        ]);
    }


    public function testUserCanNotSeeActivityLogList(): void
    {
        $this->artisan("migrate:fresh --seed");

        $this->artisan("db:seed --class=ActivityLogSeeder");

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $response = $this->actingAs($user)
            ->getJson(route('users.activities.index'));

        $response->assertStatus(403);
    }


    public function testThatNormalUserCanNotDownloadActivityLogList(): void
    {
        $this->artisan("migrate:fresh --seed");

        $this->artisan("db:seed --class=ActivityLogSeeder");

        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('users.activities.download'));

        $response->assertStatus(403);
    }



    public function testUserCanDownloadActivityLogList(): void
    {
        $this->artisan("migrate:fresh --seed");

        $this->artisan("db:seed --class=ActivityLogSeeder");

        $user = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->getJson(route('users.activities.download'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'NO',
                    'DATE',
                    'USERNAME',
                    'IP',
                    'ACTIVITY',
                    'TARGET',
                    'DESCRIPTION',
                    'DEPARTMENT',
                ],
            ]
        ]);
    }

    public function testThatUserCanFilterActivityListBasedOnDepartmentId(): void
    {
        $this->artisan("migrate:fresh --seed");

        $this->artisan("db:seed --class=ActivityLogSeeder");

        $user = User::where('username','administrator')->first();

        activity('User Login')->causedBy($user->id)
            ->performedOn($user)
            ->withProperties([
                'ip' => '127.0.0.1',
                'target' => $user->username,
                'activity' => 'Test Code Activity',
            ])
            ->log('Test Code Activity');

        $allActivities = Activity::all();

        $response = $this->actingAs($user)->getJson(route('users.activities.download', ['department_id' => $user->department_id]));

        $response->assertStatus(200);

        $deptName = Department::where('id', $user->department_id)->first()->name;
        $secondActivityName =  $allActivities->filter(function ($activity){
            return $activity->subject->department->name;
        });

        $response->assertJsonFragment([
            'DEPARTMENT' => $deptName
        ]);

        $response->assertJsonMissing([
            'DEPARTMENT' => $secondActivityName
        ]);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'NO',
                    'DATE',
                    'USERNAME',
                    'IP',
                    'ACTIVITY',
                    'TARGET',
                    'DESCRIPTION',
                    'DEPARTMENT',
                ],
            ]
        ]);
    }


}
