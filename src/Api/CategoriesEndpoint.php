<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

use malpka32\InPostBuySdk\Transport\ApiTransport;
use malpka32\InPostBuySdk\Transport\ResponseDecoder;

/**
 * Categories endpoint – API calls only, returns raw arrays.
 * Knows API URL structure: /v1/categories
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Categories
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

    private const DEPTH_MIN = 0;
    private const DEPTH_MAX = 4;

    /**
     * Browse category tree.
     *
     * @param string|null $categoryId Category ID (subtree root)
     * @param int|null    $depth      Subtree depth 0–4 (default 1)
     * @return array<string, mixed>|list<array<string, mixed>>
     */
    public function fetch(?string $categoryId = null, ?int $depth = null): array
    {
        $this->assertDepthInRange($depth);
        $params = array_filter([
            'categoryId' => $categoryId,
            'depth' => $depth,
        ], fn ($v) => $v !== null);

        $url = $this->baseUrl . self::CATEGORIES_PATH;
        if ($params !== []) {
            $url .= '?' . http_build_query($params);
        }
        $response = $this->transport->request('GET', $url);

        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * Get category details (with optional subtree depth).
     *
     * @return array<string, mixed>
     */
    public function get(string $categoryId, ?int $depth = null): array
    {
        $this->assertDepthInRange($depth);
        $url = $this->baseUrl . self::CATEGORIES_PATH . '/' . rawurlencode($categoryId);
        if ($depth !== null) {
            $url .= '?' . http_build_query(['depth' => $depth]);
        }
        $response = $this->transport->request('GET', $url);
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * Get category attributes (required and optional).
     *
     * @return list<array<string, mixed>>
     */
    public function getAttributes(string $categoryId): array
    {
        $url = $this->baseUrl . self::CATEGORIES_PATH . '/' . rawurlencode($categoryId) . '/attributes';
        $response = $this->transport->request('GET', $url);
        $data = $this->responseDecoder->decodeToArray($response);
        $filtered = array_values(array_filter($data, 'is_array'));
        /** @var list<array<string, mixed>> $filtered */
        return $filtered;
    }

    private function assertDepthInRange(?int $depth): void
    {
        if ($depth === null) {
            return;
        }
        if ($depth < self::DEPTH_MIN || $depth > self::DEPTH_MAX) {
            throw new \InvalidArgumentException(
                sprintf('Depth should be between %d and %d, got %d.', self::DEPTH_MIN, self::DEPTH_MAX, $depth)
            );
        }
    }
}
