<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSettingsRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_falls_back_to_default_branding_when_no_settings_are_set(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('QCT');
        $response->assertDontSee('fab fa-facebook-f"></i></a>', false);
    }

    public function test_homepage_reflects_a_custom_site_name_and_social_link(): void
    {
        Setting::set('site_name', 'MonSite');
        Setting::set('social_facebook', 'https://facebook.com/monsite');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('MonSite');
        $response->assertSee('https://facebook.com/monsite');
    }
}
