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
     * @return array<string, mixed>
     */
    public function get(string $offerId): array;

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

    /**
     * @return array<string, mixed>
     */
    public function close(string $offerId): array;

    /**
     * @return array<string, mixed>
     */
    public function reopen(string $offerId): array;

    /**
     * @return array<string, mixed>
     */
    public function getCommandStatus(string $commandId): array;

    /**
     * @param list<string>|null $eventType
     * @return array<string, mixed>
     */
    public function getEvents(?string $untilId = null, ?array $eventType = null, ?int $limit = null): array;

    /**
     * @return array<string, mixed>
     */
    public function getHint(?string $ean = null, ?string $mpn = null, ?string $name = null, ?int $limit = null, ?int $offset = null): array;

    /**
     * @return array<string, mixed>
     */
    public function getDepositTypes(): array;
}
