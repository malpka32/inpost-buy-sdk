<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order;

/**
 * Order status DTO for InPost update.
 */
final class OrderStatusDto
{
    /**
     * @param string|OrderUpdateStatus $status
     */
    public function __construct(
        string|OrderUpdateStatus $status,
        public ?string $comment = null,
    ) {
        $this->status = $status instanceof OrderUpdateStatus ? $status->value : $status;
    }

    public string $status;
}
