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

    public function fetch(): array
    {
        return $this->response;
    }
}
