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
    public function it_list_down_all_users()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->get('/api/users');

        $response->assertStatus(200);
        $response->assertJson([
            'users' => [
                [
                    'name' => $user1->name,
                    'email' => $user1->email
                ],
                [
                    'name' => $user2->name,
                    'email' => $user2->email
                ],
            ]
        ]);
    }

    /** @test */
    public function it_shows_deatils_of_a_user_to_authenticated_user_only()
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $user = User::factory()->create();
        $response = $this->get('/api/users/' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'user' => [
                'name' => $user->name,
                'email' => $user->email
            ],
        ]);
    }

    /** @test */
    public function it_does_not_allow_to_access_publicly()
    {
        $response = $this->get('/api/users/1');

        $response->assertStatus(302);
    }

    /** @test */
    public function it_returns_404_if_no_record_found_with_the_given_id()
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $response = $this->get('/api/users/999');

        $response->assertStatus(404);
    }
}
