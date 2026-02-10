<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

use malpka32\InPostBuySdk\Transport\ApiTransport;
use malpka32\InPostBuySdk\Transport\ResponseDecoder;

/**
 * Orders endpoint – API calls only, returns raw arrays.
 * Knows API URL structure: /v1/organizations/{orgId}/orders
 */
final class OrdersEndpoint implements OrdersEndpointInterface
{
    private const ORGANIZATION_ORDERS_PATH = '/v1/organizations/%s/orders';

    public function __construct(
        private readonly ApiTransport $transport,
        private readonly ResponseDecoder $responseDecoder,
        private readonly string $baseUrl,
        private readonly string $organizationId,
    ) {
    }

    private function ordersPath(): string
    {
        return sprintf(self::ORGANIZATION_ORDERS_PATH, rawurlencode($this->organizationId));
    }

    /**
     * @return array<string, mixed>
     */
    public function list(?string $status = null): array
    {
        $url = $this->baseUrl . $this->ordersPath();
        if ($status !== null) {
            $url .= '?' . http_build_query(['orderStatus' => $status]);
        }
        $response = $this->transport->request('GET', $url);
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * @return array<string, mixed>|null null when 404
     */
    public function get(string $orderId): ?array
    {
        $url = $this->baseUrl . $this->ordersPath() . '/' . rawurlencode($orderId);
        try {
            $response = $this->transport->request('GET', $url);
            return $this->responseDecoder->decodeToArray($response);
        } catch (\malpka32\InPostBuySdk\Exception\ApiException $e) {
            if ($e->getStatusCode() === 404) {
                return null;
            }
            throw $e;
        }
    }

    public function accept(string $orderId): void
    {
        $url = $this->baseUrl . $this->ordersPath() . '/' . rawurlencode($orderId) . '/accept';
        $this->transport->request('POST', $url, []);
    }

    public function refuse(string $orderId, string $reason = ''): void
    {
        $url = $this->baseUrl . $this->ordersPath() . '/' . rawurlencode($orderId) . '/refuse';
        $this->transport->request('POST', $url, ['reason' => $reason]);
    }
}
