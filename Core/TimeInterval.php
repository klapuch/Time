<?php
declare(strict_types = 1);
namespace Klapuch\Time;

/**
 * Interval representing time (hours, minutes, seconds)
 */
final class TimeInterval implements Interval {
	private $current;
	private $step;

	public function __construct(
		\DateTimeInterface $current,
		\DateInterval $step
	) {
		$this->current = $current;
		$this->step = $step;
	}

	public function current(): \DateTimeInterface {
		return $this->current;
	}

	public function next(): Interval {
		return new self(
			$this->current->add($this->step),
			$this->step
		);
	}

	public function iso(): string {
		return sprintf('PT%dS', $this->toSeconds($this->step));
	}

	public function __toString(): string {
		$formats = [
			's' => 'second',
			'i' => 'minute',
			'h' => 'hour',
		];
		return $this->step->format(
			implode(
				', ',
				array_reduce(
					array_keys($formats),
					function($merged, string $format) use ($formats) {
						if($this->step->{$format} !== 0)
							$merged[] = "%{$format} " . $this->withPlural(
									$this->step->{$format},
									$formats[$format]
								);
						return $merged;
					}
				)
			)
		);
	}

	private function withPlural(int $count, string $word): string {
		return $count > 1 ? $word . 's' : $word;
	}

	/**
	 * Converted step to the seconds
	 * @param \DateInterval $step
	 * @throws \InvalidArgumentException
	 * @return int
	 */
	private function toSeconds(\DateInterval $step): int {
		if($this->convertible($step))
			return $step->h * 3600 + $step->i * 60 + $step->s;
		throw new \InvalidArgumentException(
			'For time intervals are allowed only seconds, minutes and hours'
		);
	}

	/**
	 * Is the step time unit (seconds, minutes, hours)?
	 * @param \DateInterval $step
	 * @return bool
	 */
	private function convertible(\DateInterval $step): bool {
		return $step->m === 0 && $step->y === 0 && $step->d === 0;
	}
}
