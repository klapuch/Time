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

final class FutureInterval extends Tester\TestCase {
    /**
     * @throws \OutOfRangeException Next step must points to the future
     */
    public function testNextPointingToPast() {
        (new Time\FutureInterval(
            new Time\FakeInterval(
                (new \DateTimeImmutable())->add(new \DateInterval('P10D')),
                new \DateTimeImmutable('1900-01-01 01:01:01')
            )
        ))->next();
    }

    /**
     * @throws \OutOfRangeException Next step must points to the future
     */
    public function testNextPointingToNow() {
        $future = (new \DateTimeImmutable())->add(new \DateInterval('P10D'));
        (new Time\FutureInterval(
            new Time\FakeInterval(
                $future,
                $future
            )
        ))->next();
    }

    public function testNextPointingToFuture() {
        Assert::noError(function() {
            (new Time\FutureInterval(
                new Time\FakeInterval(
                    (new \DateTimeImmutable())->add(new \DateInterval('P5D')),
                    (new \DateTimeImmutable())->add(new \DateInterval('P10D'))
                )
            ))->next();
        });
    }

    /**
     * @throws \OutOfRangeException Start interval must points to the future
     */
    public function testCurrentPointingToPast() {
        (new Time\FutureInterval(
            new Time\FakeInterval(new \DateTimeImmutable('2000-01-01 01:01:01'))
        ))->current();
    }

    public function testCurrentPointingToFuture() {
        Assert::noError(function() {
            (new Time\FutureInterval(
                new Time\FakeInterval(
                    (new \DateTimeImmutable())->add(new \DateInterval('P10D'))
                )
            ))->current();
        });
    }
}

(new FutureInterval())->run();
