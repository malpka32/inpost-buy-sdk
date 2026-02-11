<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

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
     * @param list<string>|null $offerStatus Np. ['PENDING','PUBLISHED']
     * @param list<string>|null $sort        Np. ['-updatedAt']
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
        return is_array($data) ? array_values($data) : [];
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
     * Builds query string – arrays as repeated parameters (explode).
     *
     * @param array<string, mixed> $params
     */
    private static function buildQueryString(array $params): string
    {
        $pairs = [];
        foreach ($params as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $v) {
                    $pairs[] = rawurlencode((string) $key) . '=' . rawurlencode((string) $v);
                }
            } else {
                $pairs[] = rawurlencode((string) $key) . '=' . rawurlencode((string) $val);
            }
        }
        return implode('&', $pairs);
    }
}
