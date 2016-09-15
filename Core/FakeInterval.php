<?php
declare(strict_types = 1);
namespace Klapuch\Time;

/**
 * Fake
 */
final class FakeInterval implements Interval {
    private $current;
    private $next;
    private $iso;

    public function __construct(
        \DateTimeInterface $current = null,
        \DateTimeInterface $next = null,
        string $iso = null
    ) {
        $this->current = $current;
        $this->next = $next;
        $this->iso = $iso;
    }

    public function current(): \DateTimeInterface {
        return $this->current;
    }

    public function next(): Interval {
        return new self(
            $this->next,
            $this->next,
            $this->iso
        );
    }

    public function iso(): string {
        return $this->iso;
    }

    public function __toString(): string {
        return sprintf('<<%s>>', $this->iso);
    }
}
