<?php

namespace Tests\Feature;

use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    private function mockRepository()
    {
        $this->userRepository = \Mockery::mock(UserRepository::class);
        app()->instance(UserRepository::class, $this->userRepository);
    }

    /** @test */
    public function it_shows_dashboard()
    {
        $this->mockRepository();

        $mockedResponse = [['id' => 1, 'name' => 'Dummy User']];
        $this->userRepository->expects('getUserList')
            ->andReturn($mockedResponse);

        $response = $this->get(route('home'));

        $response->assertStatus(200)
                 ->assertViewIs('dashboard.index')
                 ->assertViewHas('users', $mockedResponse)
                 ->assertSeeText('Dashboard')
                 ->assertSeeText('Custom Report')
                 ->assertSeeText('Daily Work Report');
    }
}
