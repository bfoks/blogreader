<?php

namespace Tests\Feature;

use App\Blog;
use App\Platforms\Clients\Client;
use App\Platforms\Clients\FakeWP;
use App\Post;
use App\User;
use function foo\func;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BrowsingBlogsPostsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        URL::forceScheme('http');

        $this->app->instance(Client::class, new FakeWP());
    }

    /** @test */
    public function user_can_see_the_newest_post_when_opens_posts_index()
    {
        $user = factory(User::class)->create();
        $this->signIn($user);

        $blog = factory(Blog::class)->create(['user_id' => $user->id]);

        $blogPostA = factory(Post::class)->create([
            'title' => 'Post A', 'datetime_utc' => '2018-01-01 00:00:00', 'blog_id' => $blog->id,
        ]);
        $blogPostB = factory(Post::class)->create([
            'title' => 'Post B', 'datetime_utc' => '2018-01-01 01:00:00', 'blog_id' => $blog->id,
        ]);
        $blogPostC = factory(Post::class)->create([
            'title' => 'Post C', 'datetime_utc' => '2018-01-01 02:00:00', 'blog_id' => $blog->id,
        ]);

        $this->get(route('blogs.posts.index', [$blog]))
            ->assertRedirect(route('blogs.posts.show', [$blog, $blogPostC]));

    }

    /** @test */
    public function user_can_browse_blogs_posts_forward()
    {
        $user = factory(User::class)->create();
        $this->signIn($user);

        $blog = factory(Blog::class)->create(['user_id' => $user->id]);

        $blogPostA = factory(Post::class)->create([
            'title' => 'Post A', 'datetime_utc' => '2018-01-01 00:00:00', 'blog_id' => $blog->id,
        ]);
        $blogPostB = factory(Post::class)->create([
            'title' => 'Post B', 'datetime_utc' => '2018-01-01 01:00:00', 'blog_id' => $blog->id,
        ]);
        $blogPostC = factory(Post::class)->create([
            'title' => 'Post C', 'datetime_utc' => '2018-01-01 02:00:00', 'blog_id' => $blog->id,
        ]);

        $this->get(route('blogs.posts.show', [$blog, $blogPostB, 'next']))
            ->assertRedirect(route('blogs.posts.show', [$blog, $blogPostC]));

    }

    /** @test */
    public function if_user_requests_for_post_after_the_last_existing_then_gets_the_last_post_with_flash_message_and_hidden_next_button()
    {
        $user = factory(User::class)->create();
        $this->signIn($user);

        $blog = factory(Blog::class)->create(['user_id' => $user->id]);

        $blogPost = factory(Post::class)->create([
            'title' => 'Post A', 'datetime_utc' => '2018-01-01 00:00:00', 'blog_id' => $blog->id,
        ]);
        $blogPostLatest = factory(Post::class)->create([
            'title' => 'Post B', 'datetime_utc' => '2018-01-01 01:00:00', 'blog_id' => $blog->id,
        ]);

        $this->get(route('blogs.posts.show', [$blog, $blogPostLatest, 'next']))
            ->assertRedirect(route('blogs.posts.show', [$blog, $blogPostLatest]))
            ->assertSessionHas('flash_message', 'Brak nowszych wpisów.')
            ->assertSessionHas('hide_next_button', true);
    }

    /** @test */
    public function user_can_browse_blogs_posts_backward()
    {
        $user = factory(User::class)->create();
        $this->signIn($user);

        $blog = factory(Blog::class)->create(['user_id' => $user->id]);

        $blogPostA = factory(Post::class)->create([
            'title' => 'Post A', 'datetime_utc' => '2018-01-01 00:00:00', 'blog_id' => $blog->id,
        ]);
        $blogPostB = factory(Post::class)->create([
            'title' => 'Post B', 'datetime_utc' => '2018-01-01 01:00:00', 'blog_id' => $blog->id,
        ]);
        $blogPostC = factory(Post::class)->create([
            'title' => 'Post C', 'datetime_utc' => '2018-01-01 02:00:00', 'blog_id' => $blog->id,
        ]);

        $this->get(route('blogs.posts.show', [$blog, $blogPostB, 'prev']))
            ->assertRedirect(route('blogs.posts.show', [$blog, $blogPostA]));

    }

    /** @test */
    public function if_user_requests_for_post_earlier_than_the_earliest_existing_then_gets_earliest_post_with_flash_message_and_hidden_back_button()
    {
        $user = factory(User::class)->create();
        $this->signIn($user);

        $blog = factory(Blog::class)->create(['user_id' => $user->id]);

        $blogPostEarliest = factory(Post::class)->create([
            'title' => 'Post A', 'datetime_utc' => '2018-01-01 00:00:00', 'blog_id' => $blog->id,
        ]);

        $blogPost = factory(Post::class)->create([
            'title' => 'Post B', 'datetime_utc' => '2018-01-01 01:00:00', 'blog_id' => $blog->id,
        ]);

        $this->get(route('blogs.posts.show', [$blog, $blogPostEarliest, 'prev']))
            ->assertRedirect(route('blogs.posts.show', [$blog, $blogPostEarliest]))
            ->assertSessionHas('flash_message', 'Brak wcześniejszych wpisów.')
            ->assertSessionHas('hide_back_button', true);
    }

    /** @test */
    public function user_can_see_a_single_blog_post()
    {
        $user = factory(User::class)->create();
        $this->signIn($user);

        $blog = factory(Blog::class)->create(['user_id' => $user->id]);

        $blogPostA = factory(Post::class)->create([
            'title' => 'Post A', 'datetime_utc' => '2018-01-01 00:00:00', 'blog_id' => $blog->id,
        ]);
        $blogPostB = factory(Post::class)->create([
            'title' => 'Post B', 'datetime_utc' => '2018-01-01 01:00:00', 'blog_id' => $blog->id,
        ]);
        $blogPostC = factory(Post::class)->create([
            'title' => 'Post C', 'datetime_utc' => '2018-01-01 02:00:00', 'blog_id' => $blog->id,
        ]);

        $this->get(route('blogs.posts.show', [$blog, $blogPostB]))
            ->assertStatus(200)
            ->assertViewIs('blogs.posts.show')
            ->assertViewHas('post', function ($post) use ($blogPostB) {
                return $post->id === $blogPostB->id;
            });
    }
}
