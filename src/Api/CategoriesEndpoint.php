<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

use malpka32\InPostBuySdk\Transport\ApiTransport;
use malpka32\InPostBuySdk\Transport\ResponseDecoder;

/**
 * Categories endpoint – API calls only, returns raw arrays.
 * Knows API URL structure: /v1/categories
 */
final class CategoriesEndpoint implements CategoriesEndpointInterface
{
    private const CATEGORIES_PATH = '/v1/categories';

    public function __construct(
        private readonly ApiTransport $transport,
        private readonly ResponseDecoder $responseDecoder,
        private readonly string $baseUrl,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function fetch(): array
    {
        $response = $this->transport->request('GET', $this->baseUrl . self::CATEGORIES_PATH);
        return $this->responseDecoder->decodeToArray($response);
    }
}
