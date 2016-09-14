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


    protected function allowedSteps() {
        $negativeInterval = new \DateInterval('PT4M');
        $negativeInterval->invert = 1;
        return [
            [new \DateInterval('PT4M'), 240],
            [new \DateInterval('PT4S'), 4],
            [new \DateInterval('PT1H'), 3600],
            [new \DateInterval('PT1H4M4S'), 3844], // 1 hour, 4 minutes, 4 seconds
            [$negativeInterval, 240],
            [new \DateInterval('PT0M'), 0],
        ];
    }

    protected function notSupportedSteps() {
        return [
            [new \DateInterval('P1M')],
            [new \DateInterval('P1Y')],
            [new \DateInterval('P2D'), 86400 * 2],
        ];
    }

    /**
     * @dataProvider allowedSteps
     */
    public function testStepsConvertedToSeconds(
        \DateInterval $actual,
        int $expected
    ) {
        Assert::equal(
            $expected,
            (new Time\TimeInterval(
                new \DateTimeImmutable('2000-01-01 01:01:01'),
                $actual
            ))->step()
        );
    }

    /**
     * @dataProvider notSupportedSteps
     */
    public function testNotSupportedSteps(\DateInterval $actual) {
        Assert::exception(
            function() use ($actual) {
                (new Time\TimeInterval(
                    new \DateTimeImmutable('2000-01-01 01:01:01'),
                    $actual
                ))->step();
            },
            \OutOfRangeException::class,
            'For time intervals are allowed only seconds, minutes and hours'
        );
    }
}

(new TimeInterval())->run();
