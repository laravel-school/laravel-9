<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_provides_token_to_a_valid_user()
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
        $response->assertSeeText('access_token');
        $response->assertSeeText('token_type');
        $response->assertSeeText('Bearer');
    }

    /** @test */
    public function it_does_not_provide_token_with_wrong_crednetials()
    {
        User::factory()->create([
            'email' => 'foo@mail.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/api/tokens/create', [
            'email' => 'foo@mail.com',
            'password' => 'WRONG-PASSWORD'
        ]);

        $response->assertStatus(401);
        $response->assertSeeText('Invalid Credentials');
    }

    /** @test */
    public function email_field_is_required()
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
    public function email_value_must_be_a_valid_email_address()
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
    public function password_field_is_required()
    {
        User::factory()->create([
            'email' => 'foo@mail.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/api/tokens/create', [
            'email' => 'foo@mail.com',
            'password' => null,
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
