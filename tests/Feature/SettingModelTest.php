<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_default_when_key_is_missing(): void
    {
        $this->assertSame('QCT', Setting::get('site_name', 'QCT'));
    }

    public function test_set_then_get_returns_the_stored_value(): void
    {
        Setting::set('site_name', 'Mon Site');

        $this->assertSame('Mon Site', Setting::get('site_name', 'QCT'));
    }

    public function test_get_reflects_updates_after_a_second_set(): void
    {
        Setting::set('contact_email', 'first@example.com');
        $this->assertSame('first@example.com', Setting::get('contact_email'));

        Setting::set('contact_email', 'second@example.com');
        $this->assertSame('second@example.com', Setting::get('contact_email'));
    }
}
