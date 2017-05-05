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

final class LimitedInterval extends Tester\TestCase {
	public function testOversteppingLimit() {
		$now = new \DateTimeImmutable();
		$limit = [
			new Time\FakeInterval($now, null, 'PT0M'),
			new Time\FakeInterval($now, null, 'PT30M'),
		];
		Assert::exception(
			function() use ($limit) {
				(new Time\LimitedInterval(
					new Time\FakeInterval(
						new \DateTimeImmutable(),
						new \DateTimeImmutable(),
						'PT31M'
					),
					$limit
				))->current();
			},
			\OverflowException::class,
			'The range limit from <<PT0M>> to <<PT30M>> has been overstepped'
		);
		Assert::exception(
			function() use ($limit) {
				(new Time\LimitedInterval(
					new Time\FakeInterval(
						new \DateTimeImmutable(),
						new \DateTimeImmutable(),
						'PT32M'
					),
					$limit
				))->next();
			},
			\OverflowException::class,
			'The range limit from <<PT0M>> to <<PT30M>> has been overstepped'
		);
		Assert::exception(
			function() use ($limit) {
				(new Time\LimitedInterval(
					new Time\FakeInterval(
						new \DateTimeImmutable(),
						new \DateTimeImmutable(),
						'PT34M'
					),
					$limit
				))->iso();
			},
			\OverflowException::class,
			'The range limit from <<PT0M>> to <<PT30M>> has been overstepped'
		);
	}

	public function testUnderflowingLimit() {
		$now = new \DateTimeImmutable();
		$limit = [
			new Time\FakeInterval($now, null, 'PT10M'),
			new Time\FakeInterval($now, null, 'PT30M'),
		];
		Assert::exception(
			function() use ($limit) {
				(new Time\LimitedInterval(
					new Time\FakeInterval(
						new \DateTimeImmutable(),
						new \DateTimeImmutable(),
						'PT9M'
					),
					$limit
				))->current();
			},
			\UnderflowException::class,
			'The range limit from <<PT10M>> to <<PT30M>> has been underflowed'
		);
		Assert::exception(
			function() use ($limit) {
				(new Time\LimitedInterval(
					new Time\FakeInterval(
						new \DateTimeImmutable(),
						new \DateTimeImmutable(),
						'PT0M'
					),
					$limit
				))->next();
			},
			\UnderflowException::class,
			'The range limit from <<PT10M>> to <<PT30M>> has been underflowed'
		);
		Assert::exception(
			function() use ($limit) {
				(new Time\LimitedInterval(
					new Time\FakeInterval(
						new \DateTimeImmutable(),
						new \DateTimeImmutable(),
						'PT1M'
					),
					$limit
				))->iso();
			},
			\UnderflowException::class,
			'The range limit from <<PT10M>> to <<PT30M>> has been underflowed'
		);
	}

	public function testAllowedLimit() {
		$now = new \DateTimeImmutable();
		$start = new \DateTimeImmutable();
		$next = new \DateTimeImmutable();
		$iso = 'PT10M';
		Assert::same(
			$start,
			(new Time\LimitedInterval(
				new Time\FakeInterval(
					$start,
					$next,
					$iso
				),
				[
					new Time\FakeInterval($now, null, 'PT0M'),
					new Time\FakeInterval($now, null, 'PT20M'),
				]
			))->current()
		);
		Assert::same(
			$next,
			(new Time\LimitedInterval(
				new Time\FakeInterval(
					$start,
					$next,
					$iso
				),
				[
					new Time\FakeInterval($now, null, 'PT0M'),
					new Time\FakeInterval($now, null, 'PT21M'),
				]
			))->next()->current()
		);
		Assert::same(
			$iso,
			(new Time\LimitedInterval(
				new Time\FakeInterval(
					$start,
					$next,
					$iso
				),
				[
					new Time\FakeInterval($now, null, 'PT0M'),
					new Time\FakeInterval($now, null, 'PT23M'),
				]
			))->iso()
		);
	}

	public function testShuffledRangesWithCorrectReshuffling() {
		$now = new \DateTimeImmutable();
		Assert::noError(
			function() use ($now) {
				(new Time\LimitedInterval(
					new Time\FakeInterval(
						new \DateTimeImmutable(),
						new \DateTimeImmutable(),
						'PT30M'
					),
					[
						new Time\FakeInterval($now, null, 'PT40M'),
						new Time\FakeInterval($now, null, 'PT0M'),
					]
				))->current();
			}
		);
	}
}

(new LimitedInterval())->run();
