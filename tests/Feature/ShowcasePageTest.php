<?php

namespace Tests\Feature;

use Tests\TestCase;

class ShowcasePageTest extends TestCase
{
    public function test_home_page_renders_showcase_content(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Kursy walut NBP')
            ->assertSee('/api/documentation', false)
            ->assertSee('Synchronizuj walute');
    }
}
