<?php

namespace Tests\Feature;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTestimonialsDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_does_not_show_testimonials_section_when_none_are_marked(): void
    {
        Message::create(['name' => 'Jean', 'email' => 'jean@example.com', 'message' => 'Non marqué']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Ils nous font confiance');
    }

    public function test_homepage_shows_testimonials_marked_by_an_admin(): void
    {
        Message::create([
            'name' => 'Amina Traoré',
            'email' => 'amina@example.com',
            'message' => 'Mon téléphone perdu a été retrouvé grâce à QCT !',
            'is_testimonial' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Ils nous font confiance');
        $response->assertSee('Amina Traoré');
        $response->assertSee('Mon téléphone perdu a été retrouvé grâce à QCT !');
    }
}
