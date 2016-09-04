<?php
declare(strict_types = 1);
namespace Klapuch\Time;

/**
 * Interval representing datetime (day, month, year, hours, minutes, seconds)
 * Can find the next datetime by the given step
 */
final class DateTimeInterval implements Interval {
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

    public function step(): int {
        if($this->convertable($this->step))
            return $this->toSeconds($this->step);
        throw new \OutOfRangeException(
            'Months or years can not be precisely transferred'
        );
    }

    /**
     * Converted step to the seconds
     * @param \DateInterval $step
     * @return int
     */
    private function toSeconds(\DateInterval $step): int {
        return $step->d * 86400
            + $step->h * 3600
            + $step->i * 60
            + $step->s;
    }

    /**
     * Can be the step precisely converted to the seconds?
     * Years and months differs and can not be precisely calculated
     * They don't have fixed converted constant
     * @param \DateInterval $step
     * @return bool
     */
    private function convertable(\DateInterval $step): bool {
        return $step->m === 0 && $step->y === 0;
    }
}
