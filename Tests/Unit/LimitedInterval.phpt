<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Time\Unit;

use Klapuch\Time;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class LimitedInterval extends Tester\TestCase {
    public function testOversteppingLimit() {
        Assert::exception(function() {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    31
                ),
                [0, 30]
            ))->current();
        }, \OverflowException::class, 'The range limit from 0 to 30 has been overstepped');
        Assert::exception(function() {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    32
                ),
                [0, 30]
            ))->next();
        }, \OverflowException::class, 'The range limit from 0 to 30 has been overstepped');
        Assert::exception(function() {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    33
                ),
                [0, 30]
            ))->step();
        }, \OverflowException::class, 'The range limit from 0 to 30 has been overstepped');
    }

    public function testUnderflowingLimit() {
        Assert::exception(function() {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    9
                ),
                [10, 30]
            ))->current();
        }, \UnderflowException::class, 'The range limit from 10 to 30 has been underflowed');
        Assert::exception(function() {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    0
                ),
                [10, 30]
            ))->next();
        }, \UnderflowException::class, 'The range limit from 10 to 30 has been underflowed');
        Assert::exception(function() {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    -1
                ),
                [10, 30]
            ))->step();
        }, \UnderflowException::class, 'The range limit from 10 to 30 has been underflowed');
    }

    public function testAllowedLimit() {
        $start = new \DateTime();
        $next = new \DateTime();
        $step = 20;
        Assert::same(
            $start,
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    $start,
                    $next,
                    $step
                ),
                [0, 20]
            ))->current()
        );
        Assert::same(
            $next,
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    $start,
                    $next,
                    $step
                ),
                [0, 21]
            ))->next()->current()
        );
        Assert::same(
            $step,
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    $start,
                    $next,
                    $step
                ),
                [0, 22]
            ))->step()
        );
    }

    public function testShuffledRangesWithCorrectReshuffling() {
        Assert::noError(function() {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    30
                ),
                [40, 0]
            ))->current();
        });
    }
}

(new LimitedInterval())->run();
