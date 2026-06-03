<?php

namespace Tests\Feature;

use Tests\TestCase;
use Modules\Portfolio\Models\PortfolioHome;
use Modules\Blog\Models\BlogPost;
use Modules\Portfolio\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HybridPortfolioTest extends TestCase
{
    /**
     * Test the portfolio homepage (React entry point).
     */
    public function test_portfolio_home_loads(): void
    {
        $response = $this->get('/portfolio');

        $response->assertStatus(200);
        $response->assertSee('id="app"', false); // React root
        $response->assertSee('Orchestrix');
    }

    /**
     * Test the Blog index page (Blade rendered).
     */
    public function test_blog_index_loads(): void
    {
        $response = $this->get('/blog');

        $response->assertStatus(200);
        $response->assertSee('id="three-canvas"', false); // Standalone 3D canvas
        $response->assertSee('Tất cả bài viết'); // Vietnamese localization
    }

    /**
     * Test the Projects index page (Blade rendered).
     */
    public function test_projects_index_loads(): void
    {
        $response = $this->get('/portfolio/projects');

        $response->assertStatus(200);
        $response->assertSee('id="three-canvas"', false);
        $response->assertSee('Kho lưu trữ'); 
        $response->assertSee('dự án');
    }

    /**
     * Test the root redirect.
     */
    public function test_root_redirects_to_admin(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('admin.dashboard'));
    }
}
