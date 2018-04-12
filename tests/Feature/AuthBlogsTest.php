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


}
