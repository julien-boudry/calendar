<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

abstract class TestCase extends BaseTestCase
{
    public const int ITERATIONS = 2048;

    /**
     * To iterate over large ranges of test data, use a prime-number interval to
     * avoid any synchronisation problems.
     */
    public const int LARGE_PRIME = 235741;

    private const int SEED = 42;

    protected Randomizer $randomizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->randomizer = new Randomizer(new Xoshiro256StarStar(self::SEED));
    }
}
