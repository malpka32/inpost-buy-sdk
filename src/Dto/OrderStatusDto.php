<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * Order status DTO for InPost update.
 */
final class OrderStatusDto
{
    public function __construct(
        public string $status,
        public ?string $comment = null,
    ) {
    }
}
