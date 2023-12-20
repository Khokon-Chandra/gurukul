<?php

namespace Tests\Feature\Chat;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\FeatureBaseCase;
use Tests\TestCase;

class ChatTest extends FeatureBaseCase
{


    public function testThatUnauthorizedUsersCannotCreateChat(): void
    {

        $response = $this->postJson(route('social.chats.store'));

        $response->assertStatus(401);
    }

    public function testThatOnlyAuthorizedUsersCanSeeChatList(): void {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        $response = $this->actingAs($user)->getJson(route('social.chats.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'subject',
                        'receiver',
                        'date',
                        'time',
                        'created_at',
                        'updated_at'

                    ]
                ],
                'links' =>[
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links' => [
                        '*' => [
                            'url',
                            'label',
                            'active'
                        ]
                    ],
                    'path',
                    'per_page',
                    'to',
                    'total'
                ]
            ]

        ]);

    }


    public function testThatOnlyAuthorizedUsersCanCreateChat(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        $data = [
            'send_to' => 'Agent 3',
            'date' => '2023-12-01 06:27:12',
            'time' => '2023-11-01 01:17:12',
            'subject' => 'Intrusion detected'
        ];

        $response = $this->actingAs($user)->postJson(route('social.chats.store'), $data);

        $response->assertStatus(200);


        $this->assertDatabaseHas('chats', [
            'receiver' => $data['send_to'],
            'subject' => $data['subject'],
            'date' => $data['date'],
            'time' => $data['time']
        ]);

        $latestChatSaved = Chat::orderBy('id', 'desc')->first();
        $this->assertEquals($latestChatSaved->receiver, $data['send_to']);
        $this->assertEquals($latestChatSaved->subject, $data['subject']);
        $this->assertEquals($latestChatSaved->date, $data['date']);
        $this->assertEquals($latestChatSaved->time, $data['time']);



        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'subject',
                'receiver',
                'date',
                'time',
                'created_at',
                'updated_at'
            ]
        ]);
    }
}
