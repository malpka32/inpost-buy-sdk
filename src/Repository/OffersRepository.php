<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Repository;

use malpka32\InPostBuySdk\Api\OffersEndpointInterface;
use malpka32\InPostBuySdk\Collection\DepositLabelCollection;
use malpka32\InPostBuySdk\Collection\OfferCollection;
use malpka32\InPostBuySdk\Collection\OfferPutResultCollection;
use malpka32\InPostBuySdk\Dto\Offer\Command\CommandStatusDto;
use malpka32\InPostBuySdk\Dto\Offer\OfferDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferDetailsDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferEventsResultDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferHintResultDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferMetadataDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferPutResultDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\Offer\Deposit\DepositLabelMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Core\OfferCollectionMapper;

/**
 * Offers repository – endpoint + response mapping.
 * Request payload: OfferDto::toArray().
 */
final class OffersRepository
{
    public function __construct(
        private readonly OffersEndpointInterface $endpoint,
        private readonly OfferCollectionMapper $offerResponseMapper,
        private readonly DepositLabelMapper $depositLabelMapper,
    ) {
    }

    /**
     * List Offers – fetches offer list with optional pagination and filters.
     *
     * @param list<string>|null $offerStatus e.g. ['PENDING','PUBLISHED','REJECTED','CLOSED','SOLDOUT']
     * @param list<string>|null $sort        e.g. ['-updatedAt','createdAt']
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

    /**
     * Create (POST) or update (PATCH) offer.
     * Create returns OfferPutResultDto (commandId, offerId, externalId).
     * Update returns OfferDetailsDto (metadata + offer from PATCH – Offer Details).
     */
    public function putOffer(OfferDto $dto): OfferPutResultDto|OfferDetailsDto
    {
        $payload = $dto->toArray();

        if (!empty($dto->inpostOfferId)) {
            $data = $this->endpoint->update($dto->inpostOfferId, $payload);
            $metadata = null;
            if (isset($data['metadata']) && is_array($data['metadata'])) {
                $meta = $data['metadata'];
                /** @var array<string, mixed> $meta */
                $metadata = OfferMetadataDto::fromArray($meta);
            }
            $offer = ArrayHelper::extractOffer($data);
            $offerDto = $this->offerResponseMapper->mapItem($offer);
            return new OfferDetailsDto($metadata, $offerDto);
        }

        $data = $this->endpoint->create($payload);
        return OfferPutResultDto::fromArray($data);
    }

    /**
     * Sends multiple offers in a single request (Batch Offer creation).
     * Only creates new offers – API does not support batch updates.
     */
    public function putOffers(OfferCollection $offers): OfferPutResultCollection
    {
        $collection = new OfferPutResultCollection();
        if ($offers->isEmpty()) {
            return $collection;
        }

        $payload = [];
        foreach ($offers as $dto) {
            $payload[] = $dto->toArray();
        }

        $items = $this->endpoint->createBatch($payload);
        foreach ($items as $item) {
            /** @var array<string, mixed> $item */
            $collection->add(OfferPutResultDto::fromArray($item));
        }
        return $collection;
    }

    public function getOffer(string $offerId): OfferDto
    {
        $data = $this->endpoint->get($offerId);
        $offer = ArrayHelper::extractOffer($data);
        return $this->offerResponseMapper->mapItem($offer);
    }

    /**
     * Fetches offer with metadata (validationErrors, rejectionReasons).
     */
    public function getOfferDetails(string $offerId): OfferDetailsDto
    {
        $data = $this->endpoint->get($offerId);
        $metadata = null;
        if (isset($data['metadata']) && is_array($data['metadata'])) {
            $meta = $data['metadata'];
            /** @var array<string, mixed> $meta */
            $metadata = OfferMetadataDto::fromArray($meta);
        }
        $offer = ArrayHelper::extractOffer($data);
        $offerDto = $this->offerResponseMapper->mapItem($offer);

        return new OfferDetailsDto($metadata, $offerDto);
    }

    public function closeOffer(string $offerId): CommandStatusDto
    {
        $data = $this->endpoint->close($offerId);
        return CommandStatusDto::fromArray($data);
    }

    public function reopenOffer(string $offerId): CommandStatusDto
    {
        $data = $this->endpoint->reopen($offerId);
        return CommandStatusDto::fromArray($data);
    }

    public function getOfferCommandStatus(string $commandId): CommandStatusDto
    {
        $data = $this->endpoint->getCommandStatus($commandId);
        return CommandStatusDto::fromArray($data);
    }

    /**
     * @param list<string>|null $eventType
     */
    public function getOfferEvents(?string $untilId = null, ?array $eventType = null, ?int $limit = null): OfferEventsResultDto
    {
        $data = $this->endpoint->getEvents($untilId, $eventType, $limit);
        return OfferEventsResultDto::fromArray($data);
    }

    public function getOfferHint(?string $ean = null, ?string $mpn = null, ?string $name = null, ?int $limit = null, ?int $offset = null): OfferHintResultDto
    {
        $data = $this->endpoint->getHint($ean, $mpn, $name, $limit, $offset);
        return OfferHintResultDto::fromArray($data);
    }

    public function getDepositTypes(): DepositLabelCollection
    {
        $data = $this->endpoint->getDepositTypes();
        return $this->depositLabelMapper->map($data);
    }
}
