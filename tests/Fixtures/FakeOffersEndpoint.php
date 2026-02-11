<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Fixtures;

use malpka32\InPostBuySdk\Api\OffersEndpointInterface;

/**
 * Test double returning ApiMocks data instead of HTTP.
 */
final class FakeOffersEndpoint implements OffersEndpointInterface
{
    /** @var array<string, mixed> */
    private array $listResponse;

    /** @var array<string, mixed> */
    private array $createResponse;

    /** @var list<array<string, mixed>> */
    private array $createBatchResponse;

    /**
     * @param array<string, mixed> $listResponse
     * @param array<string, mixed> $createResponse
     * @param list<array<string, mixed>> $createBatchResponse
     */
    public function __construct(
        array $listResponse = [],
        array $createResponse = [],
        array $createBatchResponse = [],
    ) {
        $this->listResponse = $listResponse;
        $this->createResponse = $createResponse;
        $this->createBatchResponse = $createBatchResponse;
    }

    public function list(?array $offerStatus = null, ?int $limit = null, ?int $offset = null, ?array $sort = null): array
    {
        return $this->listResponse;
    }

    public function create(array $payload): array
    {
        return $this->createResponse;
    }

    public function createBatch(array $payload): array
    {
        return $this->createBatchResponse;
    }

    public function update(string $offerId, array $payload): array
    {
        return $this->createResponse;
    }
}
