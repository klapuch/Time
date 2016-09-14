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
        return $this->onAllowedRange(function() {
            return $this->origin->current();
        });
    }

    public function next(): Interval {
        return $this->onAllowedRange(function() {
            return $this->origin->next();
        });
    }

    public function step(): int {
        return $this->onAllowedRange(function() {
            return $this->origin->step();
        });
    }

    public function iso(): string {
        return $this->onAllowedRange(function() {
            return $this->origin->iso();
        });
    }

    public function __toString(): string {
        return (string)$this->origin;
    }

    /**
     * Call the given event on allowed range
     * @param \closure $event
     * @return int|DateTimeInterface
     * @throws \RuntimeException
     */ 
    private function onAllowedRange(\closure $event) {
        if($this->underflowed()) {
            throw new \UnderflowException(
                sprintf(
                    'The range limit %s has been underflowed',
                    $this->readableRange()
                )
            );
        }
        elseif($this->overstepped()) {
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
        return $this->origin->step() < $this->limit(self::FROM);
    }

    /**
     * Is the range overstepped?
     * @return bool
     */
    private function overstepped(): bool {
        return $this->origin->step() > $this->limit(self::TO);
    }

    /**
     * Limit by the given position
     * @param int $position
     * @return int
     */
    private function limit(int $position): int {
        return $this->orderedRange()[$position]->step();
    }

    /**
     * Human readable range
     * @return string
     */
    private function readableRange(): string {
        return sprintf(
            'from %s to %s',
            ...array_map(
                function(Interval $position) {
                    return $position->iso();
                }, $this->orderedRange()
            )
        );
    }


    /**
     * Ordered range in ascend direction
     * FROM will be always minimum
     * TO will be always maximum
     * @return array
     */
    private function orderedRange(): array {
        $positions = [
            $this->range[self::FROM]->step() => $this->range[self::FROM],
            $this->range[self::TO]->step() => $this->range[self::TO]
        ];
        return [
            self::FROM => $positions[min(array_keys($positions))],
            self::TO => $positions[max(array_keys($positions))],
        ];
    }
}
