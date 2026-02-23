<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Fixtures;

use malpka32\InPostBuySdk\Api\CategoriesEndpointInterface;

/**
 * Test double returning ApiMocks data instead of HTTP.
 */
final class FakeCategoriesEndpoint implements CategoriesEndpointInterface
{
    /** @var array<string, mixed> */
    private array $response;

    /**
     * @param array<string, mixed> $response
     */
    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    public function fetch(?string $categoryId = null, ?int $depth = null): array
    {
        return $this->response;
    }

    public function get(string $categoryId, ?int $depth = null): array
    {
        return $this->response;
    }

    public function getAttributes(string $categoryId): array
    {
        $data = $this->response['attributes'] ?? $this->response;
        return is_array($data) ? array_values(array_filter($data, 'is_array')) : [];
    }
}
