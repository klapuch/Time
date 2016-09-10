<?php
declare(strict_types = 1);
namespace Klapuch\Time;

interface Interval {
    /**
     * Current step in the interval
     * @return \DateTimeInterface
     */
    public function current(): \DateTimeInterface;

    /**
     * Next step in the interval
     * @throws \OutOfRangeException
     * @return self
     */
    public function next(): self;

    /**
     * How many units have a one step for the next interval?
     * @throws \OutOfRangeException
     * @return int
     */
    public function step(): int;

    /**
     * Steps of the interval in ISO format
     * @return string
     */
    public function iso(): string;
}
