<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPageCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new PageSeeder())->run();
    }

    public function test_non_admin_cannot_access_page_list(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/pages');

        $response->assertRedirect('/my-account');
    }

    public function test_admin_can_view_the_page_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/pages');

        $response->assertStatus(200);
        $response->assertSee('Comment ça marche');
    }

    public function test_admin_can_update_a_page_by_slug(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/pages/cgu', [
            'title' => "Conditions Générales d'Utilisation",
            'content' => '<p>Nouveau contenu CGU</p>',
        ]);

        $response->assertRedirect('admin/pages');
        $this->assertSame('<p>Nouveau contenu CGU</p>', Page::where('slug', 'cgu')->firstOrFail()->content);
    }
}
