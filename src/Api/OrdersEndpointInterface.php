<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

use malpka32\InPostBuySdk\Dto\Order\OrderEventType;
use malpka32\InPostBuySdk\Dto\Order\OrderPaymentStatus;
use malpka32\InPostBuySdk\Dto\Common\ListSort;
use malpka32\InPostBuySdk\Dto\Order\OrderStatus;

/**
 * Orders endpoint contract – enables testing with fake data.
 */
interface OrdersEndpointInterface
{
    /**
     * @param list<ListSort|string>|null $sort
     * @return array<string, mixed>
     */
    public function list(
        OrderStatus|string|null $status = null,
        OrderPaymentStatus|string|null $paymentStatus = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $sort = null,
    ): array;

    /**
     * @return array<string, mixed>|null
     */
    public function get(string $orderId): ?array;

    public function accept(string $orderId): void;

    public function refuse(string $orderId, string $reason = ''): void;

    /**
     * @return array<string, mixed>
     */
    public function getCommandStatus(string $commandId): array;

    /**
     * @param list<OrderEventType|string>|null $eventType
     * @return array<string, mixed>
     */
    public function getEvents(?string $untilId = null, ?array $eventType = null, ?int $limit = null): array;
}
