<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 * Author: Vishal Ribdiya
 * Email: vishal.ribdiya@infyom.com
 * Date: 15-07-2019
 * Time: 03:32 PM.
 */

namespace Tests\Integration\Models;

use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TimeEntryTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function get_time_entry_of_specific_user()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        factory(TimeEntry::class)->create(['user_id' => $user1->id]);
        factory(TimeEntry::class)->create(['user_id' => $user2->id]);

        $timeEntry = TimeEntry::ofUser($user1->id)->first();
        $this->assertEquals($user1->id, $timeEntry->user_id);
    }

    /** @test */
    public function get_time_entry_of_logged_in_user()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        factory(TimeEntry::class)->create(['user_id' => $user1->id]);
        factory(TimeEntry::class)->create(['user_id' => $user2->id]);
        $this->actingAs($user2); // logged in user-2

        $timeEntry = TimeEntry::ofCurrentUser()->first();
        $this->assertEquals($user2->id, $timeEntry->user_id);
    }
}
