<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Index extends TestCase
{
    /** @test */
    public function anyone_can_see_index_page()
    {
        $this->get(route('index'))
            ->assertStatus(200)
            ->assertViewIs('index')
        ;
    }
}
