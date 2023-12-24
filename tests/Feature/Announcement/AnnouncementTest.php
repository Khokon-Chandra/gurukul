<?php

namespace Tests\Feature\Announcement;

use App\Events\AnnouncementEvent;
use App\Listeners\AnnouncementListener;
use App\Models\Announcement;
use App\Models\User;
use Tests\FeatureBaseCase;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;

class AnnouncementTest extends FeatureBaseCase
{
    /**
     * Annuncement Create test example
     */
    public function testAnnouncementCreation(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->postJson(route('social.announcements.store'), [
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
                "status",
                "date",
                "created_by",
            ]
        ]);
    }


    public function testAnnouncementList(): void
    {
        $this->artisan('migrate:fresh --seed');


        $user = User::where('username','administrator')->first();


        $response = $this->actingAs($user)->getJson(route('social.announcements.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                "data" => [
                    '*' => [
                        'id',
                        'message',
                        'status',
                        'date',
                        'created_by',
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
     * Announcement single update
     */

    public function testAnnouncementUpdate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        $announcement = Announcement::first();

        $response = $this->actingAs($user)->putJson(route('social.announcements.update', $announcement->id), [
            'message' => 'update message',
            'status'  => false,
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "id",
                "message",
                "status",
                "date",
                "created_by",

            ]
        ]);
    }

    /**
     * Announcement update Multiple
     */

    public function testAnnouncementUpdateMultiple(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->putJson(route('social.announcements.update_multiple'), [
            "announcements" => [
                ['id' => 1,'message' => 'update1','status'=>true],
                ['id' => 2,'message' => 'update2','status'=>false],
                ['id' => 3,'message' => 'update3','status'=>false],
            ]
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                '*' => [
                    "id",
                    "message",
                    "status",
                    "date",
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

        $user = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->deleteJson(route('social.announcements.destroy',1));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "status",
            "message",
        ]);
    }

    public function testAnnouncementMultiDelete(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->deleteJson(route('social.announcements.delete_multiple'),[
           "announcements" => [ 1,2,3]
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
        $this->artisan('migrate:fresh --seed');

        Event::fake([
            AnnouncementEvent::class
        ]);

        $payload = [
            'message' => 'test message',
            'status' => 1,
        ];

        $user = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->postJson(route('social.announcements.store'), $payload);

        Event::assertDispatched(AnnouncementEvent::class);
    }

    public function testThatAnnouncementStatusCanBeUpdated(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        $CreateAnnouncements = Announcement::factory(3)
            ->createQuietly();


        $response = $this->actingAs($user)->patchJson(route('social.announcements.update_status'), [
            'announcement_id' =>    $CreateAnnouncements->first()->id,
        ]);

        $response->assertStatus(200);

        $updatedAnnouncement = Announcement::find(($CreateAnnouncements->first())->id);
        $allOtherAnnouncements = Announcement::where('id', '!=', ($CreateAnnouncements->first())->id)->get();


        $this->assertTrue($updatedAnnouncement->status);

        foreach ($allOtherAnnouncements as $otherAnnouncement) {
            $this->assertFalse($otherAnnouncement->status);
        }

        $response->assertJsonStructure([
            'status',
            'data' => [
                "data" => [
                    '*' => [
                        "id",
                        "message",
                        "status",
                        "date",
                        "created_by",
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

    public function testThatUserCanGetAnnouncementData(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->create()->assignRole(Role::first());

        $ActiveAnnouncement = Announcement::where('status', true)->first();

        $response = $this->actingAs($user)->getJson(route('social.announcements.data'));

        $response->assertOk();

        $response->assertSeeInOrder([$ActiveAnnouncement->number,  $ActiveAnnouncement->message]);

        $response->assertJsonStructure([
            'status',
            'data' => [
                "id",
                "message",
                "status",
                "date",
                "created_by",
            ]
        ]);
    }


    public function testUserCanActivateAnnouncement(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->getJson(route('social.announcements.activated'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' =>[
                'message',
                'status'
            ]
        ]);
    }
}
