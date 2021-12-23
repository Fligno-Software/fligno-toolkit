<?php

namespace Fligno\FlignoToolkit\Feature\Console\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class FlignoToolkitStartCommandTest
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class FlignoToolkitStartCommandTest extends TestCase
{
    /**
     * Example Test
     *
     * @test
     */
    public function example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
