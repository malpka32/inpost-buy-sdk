<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Repository;

use malpka32\InPostBuySdk\Api\OrdersEndpointInterface;
use malpka32\InPostBuySdk\Collection\OrderCollection;
use malpka32\InPostBuySdk\Dto\OrderDto;
use malpka32\InPostBuySdk\Dto\OrderStatusDto;
use malpka32\InPostBuySdk\Mapper\OrderResponseMapper;

/**
 * Orders repository – endpoint + mapping → DTO.
 */
final class OrdersRepository
{
    public function __construct(
        private readonly OrdersEndpointInterface $endpoint,
        private readonly OrderResponseMapper $mapper,
    ) {
    }

    public function getOrders(?string $status = null): OrderCollection
    {
        $data = $this->endpoint->list($status);
        return $this->mapper->map($data);
    }

    public function getOrder(string $inpostOrderId): ?OrderDto
    {
        $data = $this->endpoint->get($inpostOrderId);
        if ($data === null) {
            return null;
        }
        return $this->mapper->mapItem($data);
    }

    public function updateOrderStatus(string $inpostOrderId, OrderStatusDto $status): void
    {
        $lower = strtolower($status->status);
        if ($lower === 'accept' || $lower === 'accepted') {
            $this->endpoint->accept($inpostOrderId);
        } elseif ($lower === 'refuse' || $lower === 'refused') {
            $this->endpoint->refuse($inpostOrderId, $status->comment ?? '');
        }
    }
}
