<?php

namespace Tests\Feature\Announcement;

use App\Events\AnnouncementEvent;
use App\Listeners\AnnouncementListener;
use App\Models\Announcement;
use App\Models\User;
use App\Models\UserIp;
use Database\Factories\UserFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Spatie\Permission\Models\Role;
use Tests\FeatureBaseCase;
use Illuminate\Support\Facades\Event;


class AnnouncementTest extends FeatureBaseCase
{
    /**
     * Annuncement Create test example
     */
    public function testAnnouncementCreation(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $response = $this->actingAs($user)->postJson(route('service.announcements.store'), [
            'message' => 'Dummy text for announcement message',
            'status' => rand(0, 1)
        ]);


        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "id",
                "message",
                "number",
                "status",
                "updated_at",
                "created_at",
                "created_by",
            ]
        ]);
    }


    public function testAnnouncementList(): void
    {
        $this->artisan('migrate:fresh --seed');


        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $response = $this->actingAs($user)->getJson(route('service.announcements.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                "data" => [
                    '*' => [
                        'id',
                        'number',
                        'message',
                        'status',
                        'created_by',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links' => [
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

    /**
     * Announcement update
     */

    public function testAnnouncementUpdate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $announcements = Announcement::factory(5)->createQuietly();


        $response = $this->actingAs($user)->putJson(route('service.announcements.update'), [
            "announcements" => $announcements
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                '*' => [
                    "id",
                    "message",
                    "number",
                    "status",
                    "updated_at",
                    "created_at",
                    "created_by",
                ]
            ]
        ]);
    }


    /**
     * Delete announcement
     */

    public function testAnnouncementDelete(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();



        $response = $this->actingAs($user)->deleteJson(route('service.announcements.destroy'), [
            "announcements" => [1, 2, 3, 4]
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "status",
            "message",
        ]);
    }




    /**
     * Event attached to listener
     */
    public function testListenerIsAttachedToEvent(): void
    {
        Event::fake();
        Event::assertListening(
            AnnouncementEvent::class,
            AnnouncementListener::class
        );
    }


    /**
     * Announcement Event test
     */

    public function testAnnouncementCanBeNotify(): void
    {
        Event::fake([
            AnnouncementEvent::class
        ]);

        $payload = [
            'message' => 'test message',
            'status' => 1,
        ];

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('service.announcements.store'), $payload);

        Event::assertDispatched(AnnouncementEvent::class);
    }

    public function testThatAnnouncementStatusCanBeUpdated(): void {

        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()
            ->assignRole(Role::first());

        $announcementId = 103;

        $CreateAnnouncements = Announcement::factory(3)
            ->sequence(...[
                [
                    'id' => 103,
                    'status' => false,
                ],
                [
                    'status' => true,
                ],
                [
                    'status' => true,
                ],
            ])->createQuietly();


        $this->assertDatabaseCount('announcements', 103);


        $response = $this->actingAs($user)->patchJson(route('service.announcement.status.update'), [
            'announcement_id' =>    $announcementId,
        ]);

        $response->assertStatus(200);

        $updatedAnnouncement = Announcement::find($announcementId);
        $allOtherAnnouncements = Announcement::where('id', '!=', $announcementId)->get();


        $this->assertEquals(true,   $updatedAnnouncement->status);

        foreach ($allOtherAnnouncements as $otherAnnouncement){
            $this->assertEquals(false, $otherAnnouncement->status);
        }


        $response->assertJsonStructure([
            'status',
            'data' => [
                "data" => [
                    '*' => [
                        'id',
                        'number',
                        'message',
                        'status',
                        'created_by',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links' => [
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
}
