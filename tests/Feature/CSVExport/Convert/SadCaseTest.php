<?php

namespace Tests\Feature\CSVExport;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HappyCaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {


        $response->assertStatus(200);
    }
}
