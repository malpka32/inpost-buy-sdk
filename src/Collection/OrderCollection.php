<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\OrderDto;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of orders.
 *
 * @extends AbstractCollection<OrderDto>
 */
final class OrderCollection extends AbstractCollection
{
    public function getType(): string
    {
        return OrderDto::class;
    }

    /**
     * @param OrderDto ...$orders
     */
    public static function fromOrders(OrderDto ...$orders): self
    {
        return new self($orders);
    }

    /**
     * @param list<OrderDto> $orders
     */
    public static function fromArray(array $orders): self
    {
        return new self($orders);
    }
}
