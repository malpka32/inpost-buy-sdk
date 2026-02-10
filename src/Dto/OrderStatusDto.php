<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * DTO statusu zamówienia do aktualizacji w InPost.
 */
final class OrderStatusDto
{
    public function __construct(
        public string $status,
        public ?string $comment = null,
    ) {
    }
}
