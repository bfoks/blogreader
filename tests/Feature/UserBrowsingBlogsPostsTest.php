<?php

namespace Tests\Feature;

use App\Blog;
use App\Platforms\ClientsProvider;
use App\Platforms\FakeClientsProvider;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserBrowsingBlogsPostsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        URL::forceScheme('http');
        $this->app->instance(ClientsProvider::class, new FakeClientsProvider());

        $this->withoutExceptionHandling();

    }

    /** @test */
    public function user_can_browse_blogs_posts_forward()
    {
        $user = factory(User::class)->create();
        $this->signIn($user);

        $blog = factory(Blog::class)->create();

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

        $blog = factory(Blog::class)->create();

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

        $blog = factory(Blog::class)->create();

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

        $blog = factory(Blog::class)->create();

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

        $blog = factory(Blog::class)->create();

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

    /** @test */
    public function next_blog_posts_is_properly_saved_if_exists_on_blog()
    {
        $user = factory(User::class)->create();
        $this->signIn($user);

        $blogData = [
            'url' => 'http://example.com'
        ];

        $this->post(route('blogs.store'), $blogData);

        tap($user->fresh()->blogs->first(), function ($blog) {

            $post = $blog->posts->first();

            $this->assertEquals("First post's title", $post->title);
            $this->assertEquals(70, $post->local_id);
            $this->assertEquals('https://example.com/first-post/', $post->link);

            $this->get(route('blogs.posts.show', [$blog, $post, 'next']));

            tap($blog->fresh()->posts()->latest('datetime_utc')->first(), function ($post) {

                $this->assertEquals("Second post's title", $post->title);
                $this->assertEquals(80, $post->local_id);
                $this->assertEquals('https://example.com/second-post/', $post->link);

            });

        });

    }
}
