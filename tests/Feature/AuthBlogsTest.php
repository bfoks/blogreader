<?php

namespace Tests\Feature;

use App\Blog;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthBlogsTest extends TestCase
{

    use DatabaseMigrations;

    /* Authentication */

    /**
     * @covers BlogController::create()
     */

    /** @test */
    public function guest_is_redirected_to_login_if_wanted_to_see_create_blog_form()
    {
        $this->get(route('blogs.create'))
            ->assertRedirect(route('login'));
    }

    /**
     * @covers BlogController::destroy()
     */

    /** @test */
    public function guest_is_redirected_to_login_if_wanted_to_delete_a_blog()
    {
        $blog = factory(Blog::class)->create();

        $this->delete(route('blogs.destroy', [$blog]))
            ->assertRedirect(route('login'));
    }

    /**
     * @covers BlogController::index()
     */

    /** @test */
    public function guest_is_redirected_to_login_if_wanted_to_see_blogs_index_page()
    {
        $this->get(route('blogs.index'))
            ->assertRedirect(route('login'));
    }

    /**
     * @covers BlogController::store()
     */

    /** @test */
    public function guest_is_redirected_to_login_if_wanted_to_store_a_new_blog()
    {
        $this->post(route('blogs.store'))
            ->assertRedirect(route('login'));
    }

    /* Authorization */

    /**
     * @covers BlogController::destroy()
     */

    /** @test */
    public function user_can_delete_only_own_blog()
    {
        $owner = factory(User::class)->create();;
        $intruder = factory(User::class)->create();

        $blog = factory(Blog::class)->create(['user_id' => $owner->id]);

        $this->assertNotEmpty($owner->blogs);

        $this->actingAs($intruder)->delete(route('blogs.destroy', [$blog]))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertNotEmpty($owner->fresh()->blogs);

    }

    // Unnecessary for ['create', 'index', 'store']


}
