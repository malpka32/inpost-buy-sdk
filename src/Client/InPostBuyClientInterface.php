<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Client;

use malpka32\InPostBuySdk\Collection\AttachmentCollection;
use malpka32\InPostBuySdk\Collection\AttributeDefinitionCollection;
use malpka32\InPostBuySdk\Collection\CategoryTreeCollection;
use malpka32\InPostBuySdk\Collection\DepositLabelCollection;
use malpka32\InPostBuySdk\Collection\OfferCollection;
use malpka32\InPostBuySdk\Collection\OfferPutResultCollection;
use malpka32\InPostBuySdk\Collection\OrderCollection;
use malpka32\InPostBuySdk\Dto\Category\CategoryDetailedDto;
use malpka32\InPostBuySdk\Dto\Category\CategoryDto;
use malpka32\InPostBuySdk\Dto\Offer\Command\CommandStatusDto;
use malpka32\InPostBuySdk\Dto\Offer\OfferDto;
use malpka32\InPostBuySdk\Dto\Offer\OfferEventType;
use malpka32\InPostBuySdk\Dto\Common\ListSort;
use malpka32\InPostBuySdk\Dto\Offer\OfferStatus;
use malpka32\InPostBuySdk\Dto\Offer\Attachment\AttachmentType;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferDetailsDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferEventsResultDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferHintResultDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferPutResultDto;
use malpka32\InPostBuySdk\Dto\Order\OrderEventType;
use malpka32\InPostBuySdk\Dto\Order\OrderPaymentStatus;
use malpka32\InPostBuySdk\Dto\Order\OrderDto;
use malpka32\InPostBuySdk\Dto\Order\OrderStatus;
use malpka32\InPostBuySdk\Dto\Order\Command\OrderCommandStatusDto;
use malpka32\InPostBuySdk\Dto\Order\Response\OrderEventsResultDto;
use malpka32\InPostBuySdk\Dto\Order\OrderStatusDto;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * InPost Buy API (inpsa) client contract.
 * Documentation: https://inpsa-api-portal.inpost-group.com/
 */
interface InPostBuyClientInterface
{
    /**
     * Fetches category tree from API (same format as response: hierarchy with children).
     *
     * @param string|null $categoryId Subtree root (null = root)
     * @param int|null    $depth      Depth 0–4
     */
    public function getCategories(?string $categoryId = null, ?int $depth = null): CategoryTreeCollection;

    /**
     * Fetches category details.
     */
    public function getCategory(string $categoryId, ?int $depth = null): CategoryDetailedDto;

    /**
     * Fetches category attributes (required/optional for offers in this category).
     */
    public function getCategoryAttributes(string $categoryId): AttributeDefinitionCollection;

    /**
     * Creates or updates category in InPost (inpsa API does not support – throws exception).
     */
    public function putCategory(CategoryDto $dto): string;

    /**
     * Fetches offer list (List Offers).
     *
     * @param list<OfferStatus|string>|null $offerStatus e.g. [OfferStatus::PENDING]
     * @param list<ListSort|string>|null   $sort        e.g. [ListSort::UPDATED_AT_DESC]
     */
    public function getOffers(
        ?array $offerStatus = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $sort = null
    ): OfferCollection;

    /**
     * Sends offer (product) to InPost.
     * Create (POST) returns OfferPutResultDto (commandId, offerId, externalId).
     * Update (PATCH, when offer has inpostOfferId) returns OfferDetailsDto (metadata + offer from API).
     */
    public function putOffer(OfferDto $dto): OfferPutResultDto|OfferDetailsDto;

    /**
     * Sends multiple offers in one request (Batch Offer creation).
     */
    public function putOffers(OfferCollection $offers): OfferPutResultCollection;

    /**
     * Fetches single offer by ID.
     */
    public function getOffer(string $offerId): OfferDto;

    /**
     * Fetches offer with metadata (validationErrors, rejectionReasons).
     */
    public function getOfferDetails(string $offerId): OfferDetailsDto;

    /**
     * Closes offer. Returns command details (commandId, status).
     */
    public function closeOffer(string $offerId): CommandStatusDto;

    /**
     * Reopens offer. Returns command details (commandId, status).
     */
    public function reopenOffer(string $offerId): CommandStatusDto;

    /**
     * Gets offer command status.
     */
    public function getOfferCommandStatus(string $commandId): CommandStatusDto;

    /**
     * Gets offer events.
     *
     * @param list<OfferEventType|string>|null $eventType
     */
    public function getOfferEvents(?string $untilId = null, ?array $eventType = null, ?int $limit = null): OfferEventsResultDto;

    /**
     * Gets product hint (mapping by ean, mpn, name).
     */
    public function getOfferHint(?string $ean = null, ?string $mpn = null, ?string $name = null, ?int $limit = null, ?int $offset = null): OfferHintResultDto;

    /**
     * Lists deposit types (for offers with deposit/bail).
     */
    public function getDepositTypes(): DepositLabelCollection;

    public function getOfferAttachments(string $offerId, ?int $limit = null, ?int $offset = null): AttachmentCollection;

    /**
     * @param resource|\SplFileInfo $file
     */
    public function createOfferAttachment(string $offerId, AttachmentType|string $attachmentType, mixed $file): CommandStatusDto;

    public function downloadOfferAttachment(string $offerId, string $attachmentId): ResponseInterface;

    public function deleteOfferAttachment(string $offerId, string $attachmentId): void;

    /**
     * Fetches orders list (with optional status filter).
     *
     * @param list<ListSort|string>|null $sort
     */
    public function getOrders(
        OrderStatus|string|null $status = null,
        OrderPaymentStatus|string|null $paymentStatus = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $sort = null,
    ): OrderCollection;

    /**
     * Fetches single order by InPost ID.
     */
    public function getOrder(string $inpostOrderId): ?OrderDto;

    /**
     * Updates order status in InPost (accept / refuse).
     */
    public function updateOrderStatus(string $inpostOrderId, OrderStatusDto $status): void;

    /**
     * Gets order command status.
     */
    public function getOrderCommandStatus(string $commandId): OrderCommandStatusDto;

    /**
     * Gets order events.
     *
     * @param list<OrderEventType|string>|null $eventType
     */
    public function getOrderEvents(?string $untilId = null, ?array $eventType = null, ?int $limit = null): OrderEventsResultDto;
}
