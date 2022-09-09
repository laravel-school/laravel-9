<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_show_lists_of_users()
    {
        User::factory()->create([
            'name' => 'foo',
            'email' => 'foo@mail.com',
        ]);

        User::factory()->create([
            'name' => 'bar',
            'email' => 'bar@mail.com',
        ]);

        $response = $this->get('/api/users');

        $response->assertStatus(200);
        $response->assertJson([
            'users' => [
                [
                    "name" => "foo",
                    "email" => "foo@mail.com",
                ],
                [
                    "name" => "bar",
                    "email" => "bar@mail.com",
                ],
            ]
        ]);
    }

    /** @test */
    public function it_cannot_be_accessed_publicly()
    {
        $user = User::factory()->create([
            'name' => 'foo',
            'email' => 'foo@mail.com',
        ]);

        $response = $this->get("/api/users/$user->id");

        $response->assertStatus(302);
    }

    /** @test */
    public function only_authenticated_user_can_access_this()
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $user = User::factory()->create([
            'name' => 'foo',
            'email' => 'foo@mail.com',
        ]);

        $response = $this->get("/api/users/$user->id");

        $response->assertStatus(200);
        $response->assertJson([
            'user' => [
                'name' => 'foo',
                'email' => 'foo@mail.com',
            ]
        ]);
    }

    /** @test */
    public function it_retuns_404_when_no_user_found_with_given_id()
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $response = $this->get("/api/users/99");

        $response->assertStatus(404);
    }
}
