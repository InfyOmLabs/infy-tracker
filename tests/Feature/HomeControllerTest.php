<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function it_shows_dashboard()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200)
                 ->assertViewIs('dashboard.index')
                 ->assertSeeText('Dashboard')
                 ->assertSeeText('Custom Report')
                 ->assertSeeText('Daily Work Report');
    }
}
