<?php

namespace Tests\Feature;

use App\Platforms\ClientsProvider;
use App\Platforms\FakeClientsProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestBrowsingBlogsPostsTest extends TestCase
{

    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        URL::forceScheme('http');

        $this->withoutExceptionHandling();
        $this->app->instance(ClientsProvider::class, new FakeClientsProvider());
    }

    /** @test */
    public function guest_can_add_blog()
    {
        $this->post(route('blogs.store'), ['url' => 'http://example.com'])
            ->assertRedirect(route('blogs.posts.index', [1]));
    }

    /** @test */
    public function guest_can_see_blog_post()
    {
        $this->post(route('blogs.store'), ['url' => 'http://example.com']);

        $this->get(route('blogs.posts.show', [1, 1]))
            ->assertSee('https://example.com/first-post/');
    }


    /** @test */
    public function blogs_posts_index_display_all_blogs_posts()
    {
        $this->post(route('blogs.store'), ['url' => 'http://example.com']);

        $this->get(route('blogs.posts.index', [1]))
            ->assertViewIs('blogs.posts.index')
            ->assertViewHas('blog', function ($blog) {
                return !empty($blog->posts);
            });

    }

}
