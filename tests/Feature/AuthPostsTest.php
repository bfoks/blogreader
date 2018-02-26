<?php

namespace Tests\Feature;

use App\Blog;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthPostsTest extends TestCase
{
    use DatabaseMigrations;

    /* Authentication */


    /**
     * @covers PostController::index()
     */

    /** @test */
    public function guest_is_redirected_to_login_if_wanted_to_see_posts_index()
    {
        $blog = factory(Blog::class)->create();

        $this->get(route('blogs.posts.index', [$blog]))
            ->assertRedirect(route('login'));
    }

    /**
     * @covers PostController::show()
     */

    /** @test */
    public function guest_is_redirected_to_login_if_wanted_to_see_post()
    {
        $post = factory(Post::class)->create();

        $this->get(route('blogs.posts.show', [$post->blog, $post]))
            ->assertRedirect(route('login'));
    }


    /* Authorization */

    /**
     * @covers PostController::index()
     */

    /** @test */
    public function user_can_index_posts_only_from_own_blog()
    {
        $owner = factory(User::class)->create();
        $intruder = factory(User::class)->create();

        $blog = factory(Blog::class)->create(['user_id' => $owner->id]);
        $post = factory(Post::class)->create(['blog_id' => $blog->id]);

        $this->actingAs($intruder)
            ->get(route('blogs.posts.index', [$blog]))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }


    /**
     * @covers PostController::show()
     */

    /** @test */
    public function user_can_see_posts_only_from_own_blog()
    {
        $owner = factory(User::class)->create();
        $intruder = factory(User::class)->create();

        $blog = factory(Blog::class)->create(['user_id' => $owner->id]);
        $post = factory(Post::class)->create(['blog_id' => $blog->id]);

        $this->actingAs($intruder)
            ->get(route('blogs.posts.show', [$blog, $post]))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }


}
