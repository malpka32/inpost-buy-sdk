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

    /** @var array<string, mixed>|null */
    private ?array $getResponse;

    /**
     * @param array<string, mixed>         $listResponse
     * @param array<string, mixed>         $createResponse
     * @param list<array<string, mixed>>   $createBatchResponse
     * @param array<string, mixed>|null    $getResponse       When null, uses first offer from listResponse
     */
    public function __construct(
        array $listResponse = [],
        array $createResponse = [],
        array $createBatchResponse = [],
        ?array $getResponse = null,
    ) {
        $this->listResponse = $listResponse;
        $this->createResponse = $createResponse;
        $this->createBatchResponse = $createBatchResponse;
        $this->getResponse = $getResponse;
    }

    public function list(?array $offerStatus = null, ?int $limit = null, ?int $offset = null, ?array $sort = null): array
    {
        return $this->listResponse;
    }

    public function get(string $offerId): array
    {
        if ($this->getResponse !== null) {
            return $this->getResponse;
        }
        $data = $this->listResponse['data'] ?? $this->listResponse;
        if (is_array($data) && isset($data[0]['offer'])) {
            return $data[0];
        }
        if (is_array($data) && isset($data[0]) && is_array($data[0])) {
            return ['offer' => $data[0]];
        }
        return ['offer' => ApiMocks::singleOfferPayload()];
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

    public function close(string $offerId): array
    {
        return ['commandId' => 'cmd-close', 'status' => 'PENDING'];
    }

    public function reopen(string $offerId): array
    {
        return ['commandId' => 'cmd-reopen', 'status' => 'PENDING'];
    }

    public function getCommandStatus(string $commandId): array
    {
        return ['commandId' => $commandId, 'status' => 'COMPLETED'];
    }

    public function getEvents(?string $untilId = null, ?array $eventType = null, ?int $limit = null): array
    {
        return ['data' => []];
    }

    public function getHint(?string $ean = null, ?string $mpn = null, ?string $name = null, ?int $limit = null, ?int $offset = null): array
    {
        return ['page' => ['limit' => 10, 'offset' => 0, 'total' => 0], 'data' => []];
    }

    public function getDepositTypes(): array
    {
        return ['data' => []];
    }
}
