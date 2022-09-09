<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_provides_token()
    {
        User::factory()->create([
            'email' => 'foo@mail.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/api/tokens/create', [
            'email' => 'foo@mail.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $response->assertSeeText("access_token");
        $response->assertSeeText("Bearer");
        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function it_does_not_provides_token_with_wrong_email_and_password()
    {
        User::factory()->create([
            'email' => 'foo@mail.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/api/tokens/create', [
            'email' => 'foo@mail.com',
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function email_is_required()
    {
        User::factory()->create([
            'email' => 'foo@mail.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/api/tokens/create', [
            'email' => null,
            'password' => 'password'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            [
                'email' => [
                    'The email field is required.'
                ]
            ]
        ]);
    }

    /** @test */
    public function a_valid_email_is_required()
    {
        User::factory()->create([
            'email' => 'foo@mail.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/api/tokens/create', [
            'email' => 'foo',
            'password' => 'password'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            [
                'email' => [
                    'The email must be a valid email address.'
                ]
            ]
        ]);
    }

    /** @test */
    public function password_is_required()
    {
        User::factory()->create([
            'email' => 'foo@mail.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/api/tokens/create', [
            'email' => 'foo@mail.com',
            'password' => null
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            [
                'password' => [
                    'The password field is required.'
                ]
            ]
        ]);
    }
}
