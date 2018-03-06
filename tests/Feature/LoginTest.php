<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function logged_user_is_redirected_to_index_page_when_try_to_open_login_page()
    {
        $this->signIn();

        $this->get(route('login'))
            ->assertRedirect(route('index'));
    }
}
