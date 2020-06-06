<?php

namespace Tests\Feature\CSVExport;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SadCaseTest extends TestCase
{

    /** @test */
    public function column_header_is_missing()
    {
        $header = ['First Name', 'Last Name', 'Email Address'];
        $payload = [
            ['firstName' => 'Jon', 'lastName' => 'Doe',  'emailAddress' => 'jon@example.com']
        ];

        $response = $this->postJson('/api/csv-export', [
            'heade' => $header,
            'payload' => $payload
        ]);

        $response
            ->assertStatus(422);
    }
}
