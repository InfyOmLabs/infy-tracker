<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 * Author: Vishal Ribdiya
 * Email: vishal.ribdiya@infyom.com
 * Date: 15-07-2019
 * Time: 03:32 PM.
 */

namespace Tests\Models;

use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class TimeEntryTest.
 */
class TimeEntryTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function get_time_entry_of_specific_user()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $timeEntry1 = factory(TimeEntry::class)->create(['user_id' => $user1->id]);
        factory(TimeEntry::class)->create(['user_id' => $user2->id]);

        $timeEntries = TimeEntry::ofUser($user1->id)->get();
        $this->assertCount(1, $timeEntries);

        /** @var TimeEntry $firstTimeEntry */
        $firstTimeEntry = $timeEntries->first();
        $this->assertEquals($timeEntry1->id, $firstTimeEntry->id);
        $this->assertEquals($user1->id, $firstTimeEntry->user_id);
    }

    /** @test */
    public function get_time_entry_of_logged_in_user()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        factory(TimeEntry::class)->create(['user_id' => $user1->id]);
        $timeEntry2 = factory(TimeEntry::class)->create(['user_id' => $user2->id]);
        $this->actingAs($user2); // logged in user-2

        $timeEntries = TimeEntry::ofCurrentUser()->get();
        $this->assertCount(1, $timeEntries);

        /** @var TimeEntry $firstTimeEntry */
        $firstTimeEntry = $timeEntries->first();
        $this->assertEquals($timeEntry2->id, $firstTimeEntry->id);
        $this->assertEquals($user2->id, $firstTimeEntry->user_id);
    }
}
