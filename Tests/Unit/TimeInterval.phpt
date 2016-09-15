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

final class TimeInterval extends Tester\TestCase {
    public function testCurrent() {
        Assert::equal(
            new \DateTimeImmutable('2000-01-01 01:01:01'),
            (new Time\TimeInterval(
                new \DateTimeImmutable('2000-01-01 01:01:01'),
                new \DateInterval('PT2M')
            ))->current()
        );
    }

    public function testNextStep() {
        Assert::equal(
            new \DateTimeImmutable('2000-01-01 01:05:01'),
            (new Time\TimeInterval(
                new \DateTimeImmutable('2000-01-01 01:01:01'),
                new \DateInterval('PT4M')
            ))->next()->current()
        );
    }

    public function testIsoInSeconds() {
        Assert::equal(
            'PT120S',
            (new Time\TimeInterval(
                new \DateTimeImmutable('2000-01-01 01:01:01'),
                new \DateInterval('PT2M')
            ))->iso()
        );
    }

    /**
     * @throws \InvalidArgumentException For time intervals are allowed only seconds, minutes and hours
     */
    public function testIsoInNonTimeUnit() {
        (new Time\TimeInterval(
            new \DateTimeImmutable('2000-01-01 01:01:01'),
            new \DateInterval('P12D')
        ))->iso();
    }

    protected function formats() {
        // actual, expected
        return [
            ['PT2M', '2 minutes'],
            ['PT1M', '1 minute'],
            ['PT1S', '1 second'],
            ['PT1H', '1 hour'],
            ['PT50M', '50 minutes'],
            ['PT50S', '50 seconds'],
            ['PT50H', '50 hours'], // not convertable, only time units
            /*['PT120S', '2 minutes'],
            ['PT60S', '1 minute'],
            ['PT121S', '2 minutes, 1 second'],
            ['PT120M', '2 hours'],
            ['PT60M', '1 hour'],*/
        ];
    }

    /**
     * @dataProvider formats
     */
    public function testPrettyFormat($actual, $expected) {
        Assert::equal(
            $expected,
            (string)new Time\TimeInterval(
                new \DateTimeImmutable('2000-01-01 01:01:01'),
                new \DateInterval($actual)
            )
        );
    }
}

(new TimeInterval())->run();
