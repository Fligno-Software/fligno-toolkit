<?php

namespace Fligno\FlignoToolkit\Feature\Console\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class FlignoToolkitListPackageCommandTest
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class FlignoToolkitListPackageCommandTest extends TestCase
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
