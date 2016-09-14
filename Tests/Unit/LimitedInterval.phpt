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
        $limit = [
            new Time\FakeInterval(null, null, 0, '0'),
            new Time\FakeInterval(null, null, 30, '30')
        ];
        Assert::exception(function() use ($limit) {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    31
                ),
               $limit 
            ))->current();
        }, \OverflowException::class, 'The range limit from 0 to 30 has been overstepped');
        Assert::exception(function() use ($limit) {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    32
                ),
                $limit
            ))->next();
        }, \OverflowException::class, 'The range limit from 0 to 30 has been overstepped');
        Assert::exception(function() use ($limit) {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    33
                ),
                $limit
            ))->step();
        }, \OverflowException::class, 'The range limit from 0 to 30 has been overstepped');
        Assert::exception(function() use ($limit) {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    34
                ),
                $limit
            ))->iso();
        }, \OverflowException::class, 'The range limit from 0 to 30 has been overstepped');

    }

    public function testUnderflowingLimit() {
        $limit = [
            new Time\FakeInterval(null, null, 10, '10'),
            new Time\FakeInterval(null, null, 30, '30')
        ];
        Assert::exception(function() use ($limit) {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    9
                ),
                $limit
            ))->current();
        }, \UnderflowException::class, 'The range limit from 10 to 30 has been underflowed');
        Assert::exception(function() use ($limit) {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    0
                ),
                $limit
            ))->next();
        }, \UnderflowException::class, 'The range limit from 10 to 30 has been underflowed');
        Assert::exception(function() use ($limit) {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    -1
                ),
                $limit
            ))->step();
        }, \UnderflowException::class, 'The range limit from 10 to 30 has been underflowed');
        Assert::exception(function() use ($limit) {
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    new \DateTime(),
                    new \DateTime(),
                    -2
                ),
                $limit
            ))->iso();
        }, \UnderflowException::class, 'The range limit from 10 to 30 has been underflowed');

    }

    public function testAllowedLimit() {
        $start = new \DateTime();
        $next = new \DateTime();
        $step = 20;
        $iso = 'PT10M';
        Assert::same(
            $start,
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    $start,
                    $next,
                    $step
                ),
                [
                    new Time\FakeInterval(null, null, 0),
                    new Time\FakeInterval(null, null, 20)
                ]
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
                [
                    new Time\FakeInterval(null, null, 0),
                    new Time\FakeInterval(null, null, 21)
                ]
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
                [
                    new Time\FakeInterval(null, null, 0),
                    new Time\FakeInterval(null, null, 22)
                ]
            ))->step()
        );
        Assert::same(
            $iso,
            (new Time\LimitedInterval(
                new Time\FakeInterval(
                    $start,
                    $next,
                    $step,
                    $iso
                ),
                [
                    new Time\FakeInterval(null, null, 0),
                    new Time\FakeInterval(null, null, 23)
                ]
            ))->iso()
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
                [
                    new Time\FakeInterval(null, null, 40),
                    new Time\FakeInterval(null, null, 0)
                ]
            ))->current();
        });
    }
}

(new LimitedInterval())->run();
