<?php

namespace Tests\Platforms\Clients;

use App\Blog;
use App\Platforms\Clients\Client;
use App\Platforms\Clients\SelfHostedWP;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class SelfHostedWPTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

//        $this->withExceptionHandling();
        $this->withoutExceptionHandling();

    }

    /** @test */
    public function blog_gets_name_automatically()
    {
        $user = factory(User::class)->create();
        $this->signIn($user);

        $this->post(route('blogs.store', [
            'url' => 'https://levels.io'
        ]));

        $this->assertDatabaseHas('blogs', [
            'name' => 'levels.io',
            'url' => 'https://levels.io',
        ]);
    }

    /** @test */
    public function first_post_from_blog_is_saved_to_database()
    {
        $user = factory(User::class)->create();
        $this->signIn($user);

        $this->post(route('blogs.store', [
            'url' => 'https://levels.io'
        ]));

        $this->assertDatabaseHas('posts', [
            'local_id' => 1,
            'link' => 'https://levels.io/what-if-your-ambitions-are-too-high/',
            'title' => 'What if your ambitions are too high?',
            'datetime_utc' => '2011-10-11 00:12:01',
        ]);

    }

    /** @test */
    public function added_blog_gets_automatically_number_of_total_posts()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->signIn($user);

        $this->post(route('blogs.store', [
            'url' => 'https://levels.io'
        ]));

        /** @var Blog $blog */
        $blog = $user->blogs->first();
        $this->assertEquals('https://levels.io', $blog->url);
        $this->assertGreaterThanOrEqual(232, $blog->total_posts);
    }

    /** @test */
    public function client_gets_proper_next_post()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $this->signIn($user);

        $this->post(route('blogs.store', [
            'url' => 'https://jakoszczedzacpieniadze.pl'
        ]));

        tap($user->blogs->first(), function ($blog) {

            $this->assertDatabaseHas('posts', [
                'local_id' => 77,
                'title' => 'Budżet domowy – czyli jak zacząć oszczędzanie pieniędzy',
            ]);

            $this->get(route('blogs.posts.show', [$blog, $blog->posts->first(), 'next']));

            $this->assertDatabaseHas('posts', [
                'local_id' => 130,
                'title' => 'Koszt prądu, wody i ogrzewania – gotowy kalkulator Excel',
            ]);

            $this->get(route('blogs.posts.show', [$blog, $blog->posts()->latest('id')->first(), 'next']));

            $this->assertDatabaseHas('posts', [
                'local_id' => 170,
                'title' => 'Oszczędzanie wody: ile można zaoszczędzić na prysznicu?',
            ]);

        });

    }

    //TODO:: testy dla innych platform

    /** @test */
    public function all_blogs_posts_are_saved_to_database_after_adding_a_blog()
    {
        $this->post(route('blogs.store', [
            'url' => 'http://fashionmugging.com'
        ]));

        $blog = Blog::first();

        $this->assertGreaterThanOrEqual(232, $blog->posts->count());

    }

    // TODO:: testy dla innych platform

    /** @test */
    public function if_url_points_to_valid_blog_but_blog_has_not_any_posts_then_blog_is_not_added()
    {
        $this->post(route('blogs.store'), [
            'url' => 'https://karboosx.net/'
        ])
            ->assertSessionHas('flash_message', 'This blog has no posts');

        $this->assertEmpty(Blog::all());
    }

}
