<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Response;

/**
 * Single order event from API.
 */
final class OrderEventDto
{
    /**
     * @param array<string, mixed> $raw Raw event payload from API
     */
    public function __construct(
        private readonly array $raw,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->raw;
    }
}
