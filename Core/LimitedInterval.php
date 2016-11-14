<?php
declare(strict_types = 1);
namespace Klapuch\Time;

/**
 * Limited interval by the given range
 */
final class LimitedInterval implements Interval {
	const FROM = 0;
	const TO = 1;
	private $origin;
	private $range;

	public function __construct(Interval $origin, array $range) {
		$this->origin = $origin;
		$this->range = $range;
	}

	public function current(): \DateTimeInterface {
		return $this->onAllowedRange(
			function() {
				return $this->origin->current();
			}
		);
	}

	public function next(): Interval {
		return $this->onAllowedRange(
			function() {
				return $this->origin->next();
			}
		);
	}

	public function iso(): string {
		return $this->onAllowedRange(
			function() {
				return $this->origin->iso();
			}
		);
	}

	public function __toString(): string {
		return (string)$this->origin;
	}

	/**
	 * Call the given event on allowed range
	 * @param \Closure $event
	 * @return Interval|\DateTimeInterface|string
	 * @throws \RuntimeException
	 */
	private function onAllowedRange(\Closure $event) {
		if($this->underflowed()) {
			throw new \UnderflowException(
				sprintf(
					'The range limit %s has been underflowed',
					$this->readableRange()
				)
			);
		} elseif($this->overstepped()) {
			throw new \OverflowException(
				sprintf(
					'The range limit %s has been overstepped',
					$this->readableRange()
				)
			);
		}
		return $event();
	}

	/**
	 * Is the range underflowed?
	 * @return bool
	 */
	private function underflowed(): bool {
		return $this->comparison(
			$this->origin,
			$this->limit(self::FROM)
		) === -1;
	}

	/**
	 * Is the range overstepped?
	 * @return bool
	 */
	private function overstepped(): bool {
		return $this->comparison(
			$this->origin,
			$this->limit(self::TO)
		) === 1;
	}

	/**
	 * Compare two intervals
	 * @param Interval $left
	 * @param Interval $right
	 * @return int
	 */
	private function comparison(Interval $left, Interval $right): int {
		$now = new \DateTimeImmutable();
		return $now->add(new \DateInterval($left->iso()))
			<=> $now->add(new \DateInterval($right->iso()));
	}

	/**
	 * Part of the range by the given position
	 * @param int $position
	 * @return Interval
	 */
	private function limit(int $position): Interval {
		return $this->orderedRange()[$position];
	}

	/**
	 * Human readable range
	 * @return string
	 */
	private function readableRange(): string {
		return sprintf(
			'from %s to %s',
			...$this->orderedRange()
		);
	}

	/**
	 * Ordered range in ascend direction
	 * FROM will be always minimum
	 * TO will be always maximum
	 * @return Interval[]
	 */
	private function orderedRange(): array {
		return [
			self::FROM => min($this->range),
			self::TO => max($this->range),
		];
	}
}
