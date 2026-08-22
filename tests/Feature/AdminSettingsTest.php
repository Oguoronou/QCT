<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_settings(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/settings');

        $response->assertRedirect('/my-account');
    }

    public function test_admin_can_update_text_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'Nouveau Nom',
            'contact_email' => 'contact@nouveau.ci',
        ]);

        $response->assertRedirect('admin/settings');
        $this->assertSame('Nouveau Nom', Setting::get('site_name'));
        $this->assertSame('contact@nouveau.ci', Setting::get('contact_email'));
    }

    public function test_admin_can_upload_a_new_logo_and_the_old_one_is_removed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Setting::set('site_logo', 'uploads/settings/does-not-exist.png');

        $logo = UploadedFile::fake()->image('logo.png');
        $response = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'QCT',
            'site_logo' => $logo,
        ]);

        $response->assertRedirect('admin/settings');
        $path = Setting::get('site_logo');
        $this->assertNotSame('uploads/settings/does-not-exist.png', $path);
        $this->assertFileExists(public_path($path));

        unlink(public_path($path));
    }
}
