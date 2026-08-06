<?php

namespace Tests\Feature;

use Tests\TestCase;

class NgrokAssetUrlTest extends TestCase
{
    public function test_assets_are_generated_with_https_for_trusted_forwarded_requests(): void
    {
        $response = $this
            ->withServerVariables([
                'REMOTE_ADDR' => '127.0.0.1',
                'HTTP_HOST' => '127.0.0.1:8000',
                'HTTP_X_FORWARDED_FOR' => '203.0.113.10',
                'HTTP_X_FORWARDED_HOST' => 'ebony-incurious-unfeigningly.ngrok-free.dev',
                'HTTP_X_FORWARDED_PORT' => '443',
                'HTTP_X_FORWARDED_PROTO' => 'https',
            ])
            ->get('/');

        $response->assertOk();
        $response->assertSee('https://ebony-incurious-unfeigningly.ngrok-free.dev/build/assets/', false);
        $response->assertDontSee('http://ebony-incurious-unfeigningly.ngrok-free.dev/build/assets/', false);
    }
}
