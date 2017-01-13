<?php
declare(strict_types = 1);
namespace Klapuch\Time;

/**
 * Interval representing time (hours, minutes, seconds)
 */
final class TimeInterval implements Interval {
	const DAY = 86400; // Day in seconds
	private $current;
	private $step;

	public function __construct(
		\DateTimeImmutable $current,
		\DateInterval $step
	) {
		$this->current = $current;
		$this->step = $step;
	}

	public function current(): \DateTimeImmutable {
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
		if($this->toSeconds($this->step) >= self::DAY)
			return 'UNKNOWN';
		return implode(
			', ',
			array_map(
				function(string $time): string {
					list($number, $unit) = explode(' ', $time);
					return sprintf(
						'%01d %s',
						$number,
						$this->toPlural((int)$number, $unit)
					);
				},
				explode(',', $this->formattedSpread($this->step))
			)
		);
	}

	/**
	 * Formatted spread
	 * @param \DateInterval $step
	 * @return string
	 */
	private function formattedSpread(\DateInterval $step): string {
		return strtr(
			trim(preg_replace('~00\s.,?~', '', $this->spread($step)), ','),
			['h' => 'hour', 'm' => 'minute', 's' => 'second']
		);
	}

	/**
	 * Step spread to time (hours, minutes, seconds)
	 * @param \DateInterval $step
	 * @return string
	 */
	private function spread(\DateInterval $step): string {
		return gmdate('H \h,i \m,s \s', $this->toSeconds($this->step));
	}

	private function toPlural(int $count, string $word): string {
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