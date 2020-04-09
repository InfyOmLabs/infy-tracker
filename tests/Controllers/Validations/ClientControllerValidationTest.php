<?php

namespace Tests\Controllers\Validations;

use App\Models\Client;
use App\Models\Department;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class ClientControllerValidationTest.
 */
class ClientControllerValidationTest extends TestCase
{
    use DatabaseTransactions;

    private $defaultUserId = 1;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function test_create_client_fails_when_name_is_not_passed()
    {
        $this->post(route('clients.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_update_client_fails_when_name_is_not_passed()
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();

        $this->put(route('clients.update', $client->id), ['name' => ''])
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    /** @test */
    public function test_update_client_fails_when_name_is_duplicate()
    {
        $client1 = factory(Client::class)->create();
        $client2 = factory(Client::class)->create();

        $this->put(route('clients.update', $client2->id), ['name' => $client1->name])
            ->assertSessionHasErrors(['name' => 'The name has already been taken.']);
    }

    /** @test */
    public function test_create_client_fails_when_email_is_invalid()
    {
        $this->post(route('clients.store'), ['name' => 'Client 1', 'email' => 'dummyEmail'])
            ->assertSessionHasErrors(['email' => 'Please enter valid email.']);
    }

    /** @test */
    public function test_create_client_fails_when_website_url_is_invalid()
    {
        $this->post(route('clients.store'), ['website' => 'http::URL'])
            ->assertSessionHasErrors(['website' => 'Please enter valid url.']);
    }

    /** @test */
    public function it_can_update_client_with_valid_email_and_url()
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $department = factory(Department::class)->create();

        $this->put(route('clients.update', $client->id), [
            'name'          => $client->name,
            'email'         => 'valid.email@abc.com',
            'website'       => 'http://valid-website.com',
            'department_id' => $department->id,
        ]);

        $this->assertEquals('valid.email@abc.com', $client->fresh()->email);
        $this->assertEquals('http://valid-website.com', $client->fresh()->website);
    }

    /** @test */
    public function it_can_create_client_with_created_by_details()
    {
        $department = factory(Department::class)->create();
        $this->post(route('clients.store'),
            ['name' => 'Dummy Client', 'email' => '', 'website' => '', 'department_id' => $department->id])
            ->assertSessionHasNoErrors();

        $client = Client::whereName('Dummy Client')->first();
        $this->assertNotEmpty($client);

        $this->assertEquals('Dummy Client', $client->name);
        $this->assertEquals($this->defaultUserId, $client->created_by);
    }
}
