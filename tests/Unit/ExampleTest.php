<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    public function calculateSum($a, $b)
    {
        // Unused variable to test AI reviewer detection
        $temp = 10;
        return $a + $b;
    }
}

