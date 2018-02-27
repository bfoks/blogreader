<?php

namespace Tests\Clients;

use App\Blog;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class WordpressTest extends TestCase
{

    use DatabaseMigrations;

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
                'title' => 'Budżet domowy &#8211; czyli jak zacząć oszczędzanie pieniędzy',
            ]);

            $this->get(route('blogs.posts.show', [$blog, $blog->posts->first(), 'next']));

            $this->assertDatabaseHas('posts', [
                'local_id' => 130,
                'title' => 'Koszt prądu, wody i ogrzewania &#8211; gotowy kalkulator Excel',
            ]);

            $this->get(route('blogs.posts.show', [$blog, $blog->posts()->latest('id')->first(), 'next']));

            $this->assertDatabaseHas('posts', [
                'local_id' => 170,
                'title' => 'Oszczędzanie wody: ile można zaoszczędzić na prysznicu?',
            ]);

        });

    }

}
