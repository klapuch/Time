<?php
declare(strict_types = 1);
namespace Klapuch\Time;

/**
 * Interval which points always to the future
 * Past intervals are not allowed
 */
final class FutureInterval implements Interval {
    private $origin;

    public function __construct(Interval $origin) {
        $this->origin = $origin;
    }

    public function current(): \DateTimeInterface {
        if($this->comparison($this->origin->current(), new \DateTime()) === 1)
            return $this->origin->current();
        throw new \OutOfRangeException('Start interval must points to the future');
    }

    public function next(): Interval {
        if($this->comparison($this->origin->next()->current(), $this->current()) === 1)
            return $this->origin->next();
        throw new \OutOfRangeException('Next step must points to the future');
    }

    public function iso(): string {
        return $this->origin->iso();
    }

    public function __toString(): string {
        return (string)$this->origin;
    }

    /**
     * Compare two datetimes
     * @param \DateTimeInterface $left
     * @param \DateTimeInterface $right
     * @return int
     */
    private function comparison(
        \DateTimeInterface $left,
        \DateTimeInterface $right
    ): int {
        return $left <=> $right; 
    }
}
