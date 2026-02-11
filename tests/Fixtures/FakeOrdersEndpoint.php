<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Fixtures;

use malpka32\InPostBuySdk\Api\OrdersEndpointInterface;

/**
 * Test double returning ApiMocks data instead of HTTP.
 */
final class FakeOrdersEndpoint implements OrdersEndpointInterface
{
    /** @var array<string, mixed> */
    private array $listResponse;

    /** @var array<string, mixed>|null */
    private ?array $getResponse;

    /**
     * @param array<string, mixed> $listResponse
     * @param array<string, mixed>|null $getResponse
     */
    public function __construct(array $listResponse = [], ?array $getResponse = null)
    {
        $this->listResponse = $listResponse;
        $this->getResponse = $getResponse;
    }

    public function list(?string $status = null): array
    {
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
}
