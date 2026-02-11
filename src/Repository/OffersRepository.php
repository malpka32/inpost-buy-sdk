<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Repository;

use malpka32\InPostBuySdk\Api\OffersEndpointInterface;
use malpka32\InPostBuySdk\Collection\OfferCollection;
use malpka32\InPostBuySdk\Collection\OfferIdCollection;
use malpka32\InPostBuySdk\Dto\OfferDto;
use malpka32\InPostBuySdk\Mapper\OfferResponseMapper;

/**
 * Offers repository – endpoint + response mapping.
 * Request payload: OfferDto::toArray().
 */
final class OffersRepository
{
    public function __construct(
        private readonly OffersEndpointInterface $endpoint,
        private readonly OfferResponseMapper $offerResponseMapper,
    ) {
    }

    /**
     * List Offers – fetches offer list with optional pagination and filters.
     *
     * @param list<string>|null $offerStatus Np. ['PENDING','PUBLISHED','REJECTED','CLOSED','SOLDOUT']
     * @param list<string>|null $sort        Np. ['-updatedAt','createdAt']
     */
    public function getOffers(
        ?array $offerStatus = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $sort = null
    ): OfferCollection {
        $data = $this->endpoint->list($offerStatus, $limit, $offset, $sort);
        return $this->offerResponseMapper->map($data);
    }

    public function putOffer(OfferDto $dto): string
    {
        $payload = $dto->toArray();

        if ($dto->inpostOfferId !== null && $dto->inpostOfferId !== '') {
            $data = $this->endpoint->update($dto->inpostOfferId, $payload);
        } else {
            $data = $this->endpoint->create($payload);
        }

        return (string) ($data['id'] ?? $data['offerId'] ?? '');
    }

    /**
     * Sends multiple offers in a single request (Batch Offer creation).
     * Only creates new offers – API does not support batch updates.
     */
    public function putOffers(OfferCollection $offers): OfferIdCollection
    {
        $collection = new OfferIdCollection();
        if ($offers->isEmpty()) {
            return $collection;
        }

        $payload = [];
        foreach ($offers as $dto) {
            if ($dto instanceof OfferDto) {
                $payload[] = $dto->toArray();
            }
        }

        $items = $this->endpoint->createBatch($payload);
        foreach ($items as $item) {
            $collection->add((string) ($item['id'] ?? $item['offerId'] ?? ''));
        }
        return $collection;
    }
}
