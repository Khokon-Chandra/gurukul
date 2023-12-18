<?php

namespace Tests\Feature\Api\Chat;

use App\Models\User;
use Tests\FeatureBaseCase;

class GroupTest extends FeatureBaseCase
{
    /**
     * A group list feature test.
     */
    public function testGroupList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->getJson(route('service.groups.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'image',
                    'created_at',
                ]
            ]
        ]);
    }



    public function testGroupMemberList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->getJson(route('service.groups.members',1));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'username',
                    'last_login_at',
                ]
            ]
        ]);
    }



    public function testGroupDetails(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->getJson(route('service.groups.show', 1));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'image',
                'chats' => []
            ]
        ]);
    }




    public function testThatAuthorizeUserCanCreateGroupChat(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->postJson(route('service.groups.storeChat', 1), [
            'message' => "Hello world !!"
        ]);

        $this->assertDatabaseHas('chats', [
            'user_id' => $user->id,
            'message' => 'Hello world !!'
        ]);


        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
            'chats' => [
                '*' => [
                    'id',
                    'message',
                    'user',
                    'date',
                    'time',
                    'created_at'
                ]
            ]

        ]);
    }
}
