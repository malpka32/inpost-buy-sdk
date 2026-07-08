<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

use malpka32\InPostBuySdk\Dto\Offer\OfferEventType;
use malpka32\InPostBuySdk\Dto\Common\ListSort;
use malpka32\InPostBuySdk\Dto\Offer\OfferStatus;

/**
 * Offers endpoint contract – enables testing with fake data.
 */
interface OffersEndpointInterface
{
    /**
     * @param list<OfferStatus|string>|null $offerStatus
     * @param list<ListSort|string>|null   $sort
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
     * Batch Update Offer Price.
     *
     * @param list<array<string, mixed>> $payload
     * @return array<string, mixed>|list<mixed>
     */
    public function updatePrices(array $payload): array;

    /**
     * Batch Update Offer Stock.
     *
     * @param list<array<string, mixed>> $payload
     * @return array<string, mixed>|list<mixed>
     */
    public function updateStocks(array $payload): array;

    /**
     * Patch Offer attributes (upsert/remove).
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patchAttributes(string $offerId, array $payload): array;

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
     * @param list<OfferEventType|string>|null $eventType
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
