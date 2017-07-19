<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
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

	/**
	 * @dataProvider isoSeconds
	 */
	public function testIsoInSeconds(string $iso, string $seconds) {
		Assert::equal(
			$seconds,
			(new Time\TimeInterval(
				new \DateTimeImmutable('2000-01-01 01:01:01'),
				new \DateInterval($iso)
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

	protected function isoSeconds() {
		return [
			// ISO => seconds
			['PT2M', 'PT120S'],
			['PT1M40S', 'PT100S'],
		];
	}

	protected function singleFormats() {
		// actual, expected
		return [
			['PT2M', '2 minutes'],
			['PT1M', '1 minute'],
			['PT1S', '1 second'],
			['PT1H', '1 hour'],
			['PT50M', '50 minutes'],
			['PT50S', '50 seconds'],
			['PT50H', 'UNKNOWN'], // not convertible, only time units
			['PT25H', 'UNKNOWN'], // not convertible, only time units
			['PT24H', 'UNKNOWN'], // not convertible, only time units
			['PT120S', '2 minutes'],
			['PT60S', '1 minute'],
			['PT121S', '2 minutes, 1 second'],
			['PT120M', '2 hours'],
			['PT60M', '1 hour'],
		];
	}

	protected function multipleFormats() {
		// actual, expected
		return [
			['PT1M1S', '1 minute, 1 second'],
			['PT2M3S', '2 minutes, 3 seconds'],
			['PT1440M3S', 'UNKNOWN'], // not convertible, only time units
		];
	}

	/**
	 * @dataProvider singleFormats
	 */
	public function testPrettifiedFormats($actual, $expected) {
		Assert::equal(
			$expected,
			(string) new Time\TimeInterval(
				new \DateTimeImmutable('2000-01-01 01:01:01'),
				new \DateInterval($actual)
			)
		);
	}

	/**
	 * @dataProvider multipleFormats
	 */
	public function testPrettifiedMultipleFormats($actual, $expected) {
		Assert::equal(
			$expected,
			(string) new Time\TimeInterval(
				new \DateTimeImmutable('2000-01-01 01:01:01'),
				new \DateInterval($actual)
			)
		);
	}
}

(new TimeInterval())->run();