<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

/**
 * Orders endpoint contract – enables testing with fake data.
 */
interface OrdersEndpointInterface
{
    /**
     * @return array<string, mixed>
     */
    public function list(?string $status = null): array;

    /**
     * @return array<string, mixed>|null
     */
    public function get(string $orderId): ?array;

    public function accept(string $orderId): void;

    public function refuse(string $orderId, string $reason = ''): void;
}
