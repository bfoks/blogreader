<?php

namespace Tests\Platforms\Clients;

use App\Blog;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GlobalWPTest extends TestCase
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
            'url' => 'https://startupakademia.pl'
        ]));

        $this->assertDatabaseHas('blogs', [
            'name' => 'StartupAkademia',
            'url' => 'https://startupakademia.pl',
        ]);
    }

    /** @test */
    public function first_post_from_blog_is_saved_to_database()
    {
        $user = factory(User::class)->create();
        $this->signIn($user);

        $this->post(route('blogs.store', [
            'url' => 'https://startupakademia.pl'
        ]));

        $this->assertDatabaseHas('posts', [
            'local_id' => 6,
            'link' => 'https://startupakademia.pl/2014/07/07/jak-oszacowac-rynek/',
            'title' => 'Jak oszacować rynek?',
            'datetime_utc' => '2014-07-07 14:43:25',
        ]);

    }

    /** @test */
    public function added_blog_gets_automatically_number_of_total_posts()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->signIn($user);

        $this->post(route('blogs.store', [
            'url' => 'https://startupakademia.pl'
        ]));

        /** @var Blog $blog */
        $blog = $user->blogs->first();
        $this->assertEquals('https://startupakademia.pl', $blog->url);
        $this->assertGreaterThanOrEqual(335, $blog->total_posts);
    }

    /** @test */
    public function client_gets_proper_next_post()
    {

        $user = factory(User::class)->create();
        $this->signIn($user);

        $this->post(route('blogs.store', [
            'url' => 'https://startupakademia.pl'
        ]));

        tap($user->blogs->first(), function ($blog) {

            $this->assertDatabaseHas('posts', [
                'local_id' => 6,
                'title' => 'Jak oszacować rynek?',
            ]);

            $this->get(route('blogs.posts.show', [$blog, $blog->posts->first(), 'next']));

            $this->assertDatabaseHas('posts', [
                'local_id' => 16,
                'title' => 'Wielkość Kwejka czyli jak zarabiać na śmiesznych obrazkach&#8230;.',
            ]);

            $this->get(route('blogs.posts.show', [$blog, $blog->posts()->latest('id')->first(), 'next']));

            $this->assertDatabaseHas('posts', [
                'local_id' => 21,
                'title' => 'Zaskocz klientów i Partnera prostym&nbsp;filmikiem',
            ]);

        });

    }

}
