<?php

namespace Fligno\FlignoToolkit\Feature\Console\Commands;

use Tests\TestCase;

/**
 * Class FlignoToolkitLoginCommandTest
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class FlignoToolkitLoginCommandTest extends TestCase
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
