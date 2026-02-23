<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Transport\ApiTransport;
use malpka32\InPostBuySdk\Transport\ResponseDecoder;

/**
 * Offers endpoint – API calls only, returns raw arrays.
 * Knows API URL structure: /v1/organizations/{orgId}/offers
 */
final class OffersEndpoint implements OffersEndpointInterface
{
    private const ORGANIZATION_OFFERS_PATH = '/v1/organizations/%s/offers';

    public function __construct(
        private readonly ApiTransport $transport,
        private readonly ResponseDecoder $responseDecoder,
        private readonly string $baseUrl,
        private readonly string $organizationId,
    ) {
    }

    private function offersPath(?string $offerId = null): string
    {
        $path = sprintf(self::ORGANIZATION_OFFERS_PATH, rawurlencode($this->organizationId));
        return $offerId !== null ? $path . '/' . rawurlencode($offerId) : $path;
    }

    /**
     * List Offers – offer list with pagination.
     *
     * @param list<string>|null $offerStatus e.g. ['PENDING','PUBLISHED']
     * @param list<string>|null $sort        e.g. ['-updatedAt']
     * @return array<string, mixed> { page: { limit, offset, total }, data: OfferDetails[] }
     */
    public function list(?array $offerStatus = null, ?int $limit = null, ?int $offset = null, ?array $sort = null): array
    {
        $params = array_filter([
            'offerStatus' => $offerStatus,
            'limit' => $limit,
            'offset' => $offset,
            'sort' => $sort,
        ], fn ($v) => $v !== null);

        $url = $this->baseUrl . $this->offersPath();
        if ($params !== []) {
            $query = self::buildQueryString($params);
            $url .= '?' . $query;
        }

        $response = $this->transport->request('GET', $url);
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * Create new Offer (single offer).
     *
     * @param array<string, mixed> $payload OfferProposal
     * @return array<string, mixed> OfferCreated
     */
    public function create(array $payload): array
    {
        $response = $this->transport->request('POST', $this->baseUrl . $this->offersPath(), $payload);
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * Batch Offer creation – multiple offers in one request.
     *
     * @param list<array<string, mixed>> $payload BatchOffersProposal (array of OfferProposal)
     * @return list<array<string, mixed>> BatchOffersCreated (array of OfferCreated)
     */
    public function createBatch(array $payload): array
    {
        $path = sprintf(self::ORGANIZATION_OFFERS_PATH, rawurlencode($this->organizationId)) . '/batch';
        $response = $this->transport->request('POST', $this->baseUrl . $path, $payload);
        $data = $this->responseDecoder->decodeToArray($response);
        $list = [];
        foreach ($data as $v) {
            if (is_array($v)) {
                $list[] = $v;
            }
        }
        /** @var list<array<string, mixed>> $list */
        return $list;
    }

    /**
     * Get single Offer details.
     *
     * @return array<string, mixed> { metadata?, offer: Offer }
     */
    public function get(string $offerId): array
    {
        $response = $this->transport->request('GET', $this->baseUrl . $this->offersPath($offerId));
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function update(string $offerId, array $payload): array
    {
        $response = $this->transport->request('PATCH', $this->baseUrl . $this->offersPath($offerId), $payload);
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * Close Offer (state → CLOSED). Returns CommandDetails.
     *
     * @return array<string, mixed> { commandId, status }
     */
    public function close(string $offerId): array
    {
        $response = $this->transport->request('POST', $this->baseUrl . $this->offersPath($offerId) . '/close');
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * Reopen Offer. Returns CommandDetails.
     *
     * @return array<string, mixed> { commandId, status }
     */
    public function reopen(string $offerId): array
    {
        $response = $this->transport->request('POST', $this->baseUrl . $this->offersPath($offerId) . '/reopen');
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * Get Offer command status.
     *
     * @return array<string, mixed> { commandId, status }
     */
    public function getCommandStatus(string $commandId): array
    {
        $path = sprintf(self::ORGANIZATION_OFFERS_PATH, rawurlencode($this->organizationId))
            . '/commands/' . rawurlencode($commandId);
        $response = $this->transport->request('GET', $this->baseUrl . $path);
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * List Offer events.
     *
     * @param list<string>|null $eventType
     * @return array<string, mixed> { data: OfferEvent[] }
     */
    public function getEvents(?string $untilId = null, ?array $eventType = null, ?int $limit = null): array
    {
        $params = array_filter([
            'untilId' => $untilId,
            'eventType' => $eventType,
            'limit' => $limit,
        ], fn ($v) => $v !== null);
        $path = sprintf(self::ORGANIZATION_OFFERS_PATH, rawurlencode($this->organizationId)) . '/events';
        $url = $this->baseUrl . $path;
        if ($params !== []) {
            $url .= '?' . self::buildQueryString($params);
        }
        $response = $this->transport->request('GET', $url);
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * Hint – product info, GPSR info, category mapping (by ean, mpn, name).
     *
     * @return array<string, mixed> { page, data: ProductHint[] }
     */
    public function getHint(?string $ean = null, ?string $mpn = null, ?string $name = null, ?int $limit = null, ?int $offset = null): array
    {
        $params = array_filter([
            'ean' => $ean,
            'mpn' => $mpn,
            'name' => $name,
            'limit' => $limit,
            'offset' => $offset,
        ], fn ($v) => $v !== null);
        $path = sprintf(self::ORGANIZATION_OFFERS_PATH, rawurlencode($this->organizationId)) . '/hint';
        $url = $this->baseUrl . $path;
        if ($params !== []) {
            $url .= '?' . http_build_query($params);
        }
        $response = $this->transport->request('GET', $url);
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * List Deposit Types (global, no orgId).
     *
     * @return array<string, mixed> { data: DepositLabel[] }
     */
    public function getDepositTypes(): array
    {
        $response = $this->transport->request('GET', $this->baseUrl . '/v1/offers/deposit-types');
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * Builds query string – arrays as repeated parameters (explode).
     *
     * @param array<string, mixed> $params
     */
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
