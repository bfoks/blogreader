<?php

namespace Tests\Platforms;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class DiscovererTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->withoutExceptionHandling();

    }

    /** @test */
    public function discoverer_recognize_properly_different_platforms()
    {
        $user = factory(User::class)->create();
        $this->signIn($user);

        $this->post(route('blogs.store', [
            'url' => 'https://levels.io'
        ]));

        $this->post(route('blogs.store', [
            'url' => 'https://startupakademia.pl'
        ]));



        // for SelfHostedWP
        $this->assertDatabaseHas('blogs', [
            'name' => 'levels.io',
            'url' => 'https://levels.io',
        ]);
        $this->assertDatabaseHas('posts', [
            'local_id' => 1,
            'link' => 'https://levels.io/what-if-your-ambitions-are-too-high/',
            'title' => 'What if your ambitions are too high?',
            'datetime_utc' => '2011-10-11 00:12:01',
        ]);


        // for GlobalWP
        $this->assertDatabaseHas('blogs', [
            'name' => 'StartupAkademia',
            'url' => 'https://startupakademia.pl',
        ]);
        $this->assertDatabaseHas('posts', [
            'local_id' => 6,
            'link' => 'https://startupakademia.pl/2014/07/07/jak-oszacowac-rynek/',
            'title' => 'Jak oszacować rynek?',
            'datetime_utc' => '2014-07-07 14:43:25',
        ]);
    }

}
