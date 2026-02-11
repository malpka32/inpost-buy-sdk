<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * Order DTO from InPost Buy API.
 */
final class OrderDto
{
    public function __construct(
        public string $inpostOrderId,
        public ?string $status = null,
        public ?string $reference = null,
        public ?\DateTimeInterface $createdAt = null,
        public ?\DateTimeInterface $updatedAt = null,
        public ?array $items = null,
        public ?array $raw = null,
    ) {
    }
}
