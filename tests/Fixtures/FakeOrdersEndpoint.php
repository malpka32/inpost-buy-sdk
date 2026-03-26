<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Fixtures;

use malpka32\InPostBuySdk\Api\OrdersEndpointInterface;
use malpka32\InPostBuySdk\Dto\Order\OrderPaymentStatus;
use malpka32\InPostBuySdk\Dto\Common\ListSort;
use malpka32\InPostBuySdk\Dto\Order\OrderStatus;

/**
 * Test double returning ApiMocks data instead of HTTP.
 */
final class FakeOrdersEndpoint implements OrdersEndpointInterface
{
    /** @var array<string, mixed> */
    private array $listResponse;

    /** @var array<string, mixed>|null */
    private ?array $getResponse;

    /** @var array{status: OrderStatus|string|null, paymentStatus: OrderPaymentStatus|string|null, limit: int|null, offset: int|null, sort: list<ListSort|string>|null}|null */
    public ?array $lastListCall = null;

    /** @var array<string, mixed> */
    private array $eventsResponse;

    /**
     * @param array<string, mixed> $listResponse
     * @param array<string, mixed>|null $getResponse
     * @param array<string, mixed> $eventsResponse
     */
    public function __construct(array $listResponse = [], ?array $getResponse = null, array $eventsResponse = ['data' => []])
    {
        $this->listResponse = $listResponse;
        $this->getResponse = $getResponse;
        $this->eventsResponse = $eventsResponse;
    }

    public function list(
        OrderStatus|string|null $status = null,
        OrderPaymentStatus|string|null $paymentStatus = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $sort = null,
    ): array {
        /** @var list<ListSort|string>|null $sort */
        $this->lastListCall = [
            'status' => $status,
            'paymentStatus' => $paymentStatus,
            'limit' => $limit,
            'offset' => $offset,
            'sort' => $sort,
        ];
        return $this->listResponse;
    }

    public function get(string $orderId): ?array
    {
        return $this->getResponse;
    }

    public function accept(string $orderId): void
    {
    }

    public function refuse(string $orderId, string $reason = ''): void
    {
    }

    public function getCommandStatus(string $commandId): array
    {
        return ['commandId' => $commandId, 'status' => 'COMPLETED'];
    }

    public function getEvents(?string $untilId = null, ?array $eventType = null, ?int $limit = null): array
    {
        return $this->eventsResponse;
    }
}
