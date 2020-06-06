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

        $header = ['First Name', 'Last Name', 'Email Address'];
        $payload = [
            ['firstName' => 'Jon', 'lastName' => 'Doe',  'emailAddress' => 'jon@example.com']
        ];

        $this->response = $this->request($header, $payload);
    }

    protected function request($header, $payload) {

        $res =  $this->postJson('/api/csv-export', [
            'header' => $header,
            'payload' => $payload
        ]);

        $res->streamedContent();

        return $res;
    }

    /** @test */
    public function success_response()
    {
        $this->response->assertStatus(200);
    }

    /** @test */
    public function response_content_must_be_in_csv()
    {
        $content = $this->response->streamedContent();

        $this->assertStringContainsString('First Name', $content);
        $this->response->assertHeader('Content-type');

    }
}
