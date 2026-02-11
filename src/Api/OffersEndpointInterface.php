<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

/**
 * Offers endpoint contract – enables testing with fake data.
 */
interface OffersEndpointInterface
{
    /**
     * @param list<string>|null $offerStatus
     * @param list<string>|null $sort
     * @return array<string, mixed>
     */
    public function list(?array $offerStatus = null, ?int $limit = null, ?int $offset = null, ?array $sort = null): array;

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload): array;

    /**
     * @param list<array<string, mixed>> $payload
     * @return list<array<string, mixed>>
     */
    public function createBatch(array $payload): array;

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function update(string $offerId, array $payload): array;
}
