<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Client;

use malpka32\InPostBuySdk\Collection\CategoryCollection;
use malpka32\InPostBuySdk\Collection\OfferCollection;
use malpka32\InPostBuySdk\Collection\OfferIdCollection;
use malpka32\InPostBuySdk\Collection\OrderCollection;
use malpka32\InPostBuySdk\Dto\CategoryDto;
use malpka32\InPostBuySdk\Dto\OfferDto;
use malpka32\InPostBuySdk\Dto\OrderDto;
use malpka32\InPostBuySdk\Dto\OrderStatusDto;

/**
 * InPost Buy API (inpsa) client contract.
 * Documentation: https://inpsa-api-portal.inpost-group.com/
 */
interface InPostBuyClientInterface
{
    /**
     * Fetches category list (tree or flat list).
     */
    public function getCategories(): CategoryCollection;

    /**
     * Creates or updates category in InPost (inpsa API does not support – throws exception).
     */
    public function putCategory(CategoryDto $dto): string;

    /**
     * Fetches offer list (List Offers).
     *
     * @param list<string>|null $offerStatus e.g. ['PENDING','PUBLISHED']
     * @param list<string>|null $sort        e.g. ['-updatedAt']
     */
    public function getOffers(
        ?array $offerStatus = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $sort = null
    ): OfferCollection;

    /**
     * Sends offer (product) to InPost. Returns offer ID.
     */
    public function putOffer(OfferDto $dto): string;

    /**
     * Sends multiple offers in one request (Batch Offer creation).
     */
    public function putOffers(OfferCollection $offers): OfferIdCollection;

    /**
     * Fetches orders list (with optional status filter).
     */
    public function getOrders(?string $status = null): OrderCollection;

    /**
     * Fetches single order by InPost ID.
     */
    public function getOrder(string $inpostOrderId): ?OrderDto;

    /**
     * Updates order status in InPost (accept / refuse).
     */
    public function updateOrderStatus(string $inpostOrderId, OrderStatusDto $status): void;
}
