<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

use malpka32\InPostBuySdk\Dto\Order\OrderEventType;
use malpka32\InPostBuySdk\Dto\Order\OrderPaymentStatus;
use malpka32\InPostBuySdk\Dto\Common\ListSort;
use malpka32\InPostBuySdk\Dto\Order\OrderStatus;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
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
     * @param list<ListSort|string>|null $sort
     * @return array<string, mixed>
     */
    public function list(
        OrderStatus|string|null $status = null,
        OrderPaymentStatus|string|null $paymentStatus = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $sort = null,
    ): array
    {
        $statusParam = self::normalizeBackedEnumOrString($status);
        $paymentStatusParam = self::normalizeBackedEnumOrString($paymentStatus);
        $sortParam = self::normalizeStringList($sort);
        $url = $this->baseUrl . $this->ordersPath();
        $params = array_filter([
            'orderStatus' => $statusParam,
            'paymentStatus' => $paymentStatusParam,
            'limit' => $limit,
            'offset' => $offset,
            'sort' => $sortParam,
        ], static fn (mixed $v): bool => $v !== null);
        if ($params !== []) {
            $url .= '?' . self::buildQueryString($params);
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

    /**
     * @return array<string, mixed>
     */
    public function getCommandStatus(string $commandId): array
    {
        $path = sprintf(self::ORGANIZATION_ORDERS_PATH, rawurlencode($this->organizationId))
            . '/commands/' . rawurlencode($commandId);
        $response = $this->transport->request('GET', $this->baseUrl . $path);

        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * @param list<OrderEventType|string>|null $eventType
     * @return array<string, mixed>
     */
    public function getEvents(?string $untilId = null, ?array $eventType = null, ?int $limit = null): array
    {
        $eventTypeParam = self::normalizeStringList($eventType);
        $params = array_filter([
            'untilId' => $untilId,
            'eventType' => $eventTypeParam,
            'limit' => $limit,
        ], static fn (mixed $v): bool => $v !== null);

        $path = sprintf(self::ORGANIZATION_ORDERS_PATH, rawurlencode($this->organizationId)) . '/events';
        $url = $this->baseUrl . $path;
        if ($params !== []) {
            $url .= '?' . self::buildQueryString($params);
        }

        $response = $this->transport->request('GET', $url);
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * @param list<\BackedEnum|string>|null $values
     * @return list<string>|null
     */
    private static function normalizeStringList(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $normalized = [];
        foreach ($values as $value) {
            if ($value instanceof \BackedEnum) {
                $value = $value->value;
            }
            $asString = ArrayHelper::asString($value);
            if ($asString === '') {
                continue;
            }
            $normalized[] = $asString;
        }

        return $normalized === [] ? null : $normalized;
    }

    private static function normalizeBackedEnumOrString(\BackedEnum|string|null $value): ?string
    {
        if ($value instanceof \BackedEnum) {
            return ArrayHelper::asString($value->value);
        }
        if ($value === null) {
            return null;
        }

        $asString = ArrayHelper::asString($value);
        return $asString === '' ? null : $asString;
    }

    /**
     * @param array<string, mixed> $params
     */
    private static function buildQueryString(array $params): string
    {
        $pairs = [];
        foreach ($params as $key => $val) {
            $keyStr = ArrayHelper::asString($key);
            if (is_array($val)) {
                foreach ($val as $v) {
                    $pairs[] = rawurlencode($keyStr) . '=' . rawurlencode(ArrayHelper::asString($v));
                }
            } else {
                $pairs[] = rawurlencode($keyStr) . '=' . rawurlencode(ArrayHelper::asString($val));
            }
        }

        return implode('&', $pairs);
    }
}
