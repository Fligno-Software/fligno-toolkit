<?php

namespace Fligno\FlignoToolkit\Feature\Console\Commands;

use Tests\TestCase;

/**
 * Class FlignoToolkitGroupCommandTest
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class FlignoToolkitGroupCommandTest extends TestCase
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
