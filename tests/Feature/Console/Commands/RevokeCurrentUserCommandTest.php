<?php

namespace Fligno\FlignoToolkit\Feature\Console\Commands;

use Tests\TestCase;

/**
 * Class RevokeCurrentUserCommandTest
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class RevokeCurrentUserCommandTest extends TestCase
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
