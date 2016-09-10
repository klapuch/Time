<?php
declare(strict_types = 1);
namespace Klapuch\Time;

/**
 * Fake
 */
final class FakeInterval implements Interval {
    private $current;
    private $next;
    private $step;
    private $iso;

    public function __construct(
        \DateTimeInterface $current = null,
        \DateTimeInterface $next = null,
        int $step = null,
        string $iso = null
    ) {
        $this->current = $current;
        $this->next = $next;
        $this->step = $step;
        $this->iso = $iso;
    }

    public function current(): \DateTimeInterface {
        return $this->current;
    }

    public function next(): Interval {
        return new self(
            $this->next,
            $this->next,
            $this->step,
            $this->iso
        );
    }

    public function step(): int {
        return $this->step;
    }

    public function iso(): string {
        return $this->iso;
    }
}
