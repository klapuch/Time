<?php
declare(strict_types = 1);
namespace Klapuch\Time;

interface Interval {
	/**
	 * Current step in the interval
	 * @return \DateTimeImmutable
	 */
	public function current(): \DateTimeImmutable;

	/**
	 * Next step in the interval
	 * @throws \OutOfRangeException
	 * @return self
	 */
	public function next(): self;

	/**
	 * The interval in ISO format
	 * @return string
	 */
	public function iso(): string;

	/**
	 * Print itself
	 * @return string
	 */
	public function __toString(): string;
}
