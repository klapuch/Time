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

	public function __construct(
		\DateTimeInterface $current = null,
		\DateTimeInterface $next = null,
		int $step = null
	) {
		$this->current = $current;
		$this->next = $next;
		$this->step = $step;
	}

	public function current(): \DateTimeInterface {
		return $this->current;
	}

    public function next(): Interval {
        return new self(
            $this->next,
            $this->next,
            $this->step
        );
	}

	public function step(): int {
		return $this->step;
	}
}
