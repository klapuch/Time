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

final class DateTimeInterval extends Tester\TestCase {
    public function testCurrent() {
        Assert::equal(
            new \DateTimeImmutable('2000-01-01 01:01:01'),
            (new Time\DateTimeInterval(
                new \DateTimeImmutable('2000-01-01 01:01:01'),
                new \DateInterval('PT2M')
            ))->current()
        );
    }

    public function testNextStep() {
        Assert::equal(
            new \DateTimeImmutable('2000-01-01 01:05:01'),
            (new Time\DateTimeInterval(
                new \DateTimeImmutable('2000-01-01 01:01:01'),
                new \DateInterval('PT4M')
            ))->next()->current()
        );
    }

    public function testIsoInSeconds() {
        Assert::equal(
            'PT120S',
            (new Time\DateTimeInterval(
                new \DateTimeImmutable('2000-01-01 01:01:01'),
                new \DateInterval('PT2M')
            ))->iso()
        );

    }

    protected function allowedSteps() {
        $negativeInterval = new \DateInterval('PT4M');
        $negativeInterval->invert = 1;
        return [
            [new \DateInterval('PT4M'), 240],
            [new \DateInterval('PT4S'), 4],
            [new \DateInterval('PT1H'), 3600],
            [new \DateInterval('P2D'), 86400 * 2],
            [new \DateInterval('PT1H4M4S'), 3844], // 1 hour, 4 minutes, 4 seconds
            [$negativeInterval, 240],
            [new \DateInterval('PT0M'), 0],
        ];
    }

    protected function notSupportedSteps() {
        return [
            [new \DateInterval('P1M')],
            [new \DateInterval('P1Y')],
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
            (new Time\DateTimeInterval(
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
                (new Time\DateTimeInterval(
                    new \DateTimeImmutable('2000-01-01 01:01:01'),
                    $actual
                ))->step();
            },
            \OutOfRangeException::class,
            'Months and years can not be precisely converted'
        );
    }
}

(new DateTimeInterval())->run();
