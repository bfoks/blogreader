<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PusherTest extends TestCase
{
    /** @test */
    public function page_contains_csrf_token_required_by_laravel_echo()
    {
        $this->get(route('index'))
            ->assertSee('<meta name="csrf-token"');
    }
}
