<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * Order DTO from InPost Buy API.
 */
final class OrderDto
{
    /**
     * @param list<array<string, mixed>>|null $items
     * @param array<string, mixed>|null $raw
     */
    public function __construct(
        public string $inpostOrderId,
        public ?string $status = null,
        public ?string $reference = null,
        public ?\DateTimeInterface $createdAt = null,
        public ?\DateTimeInterface $updatedAt = null,
        /** @var list<array<string, mixed>>|null */
        public ?array $items = null,
        /** @var array<string, mixed>|null */
        public ?array $raw = null,
    ) {
    }
}
