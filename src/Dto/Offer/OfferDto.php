<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer;

use malpka32\InPostBuySdk\Dto\Offer\Gpsr\GpsrInfoDto;
use malpka32\InPostBuySdk\Dto\Offer\Product\ProductDto;

/**
 * InPost Buy offer (OpenAPI: OfferProposal / Offer).
 *
 * Full offer definition for create or update.
 * Used both as payload for POST/PATCH offers and as API response representation.
 *
 * Required fields when creating: product, stock, price.
 * externalId – external identifier (e.g. SKU), set by integrator.
 * inpostOfferId – InPost offer ID (only in API response or when updating).
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class OfferDto
{
    public function __construct(
        /** External offer identifier (e.g. SKU from seller system). */
        public string $externalId,
        /** Product data – proposal for InPost catalog matching. */
        public ProductDto $product,
        /** Stock level. */
        public StockDto $stock,
        /** Offer price (gross, VAT, optional deposits). */
        public PriceDto $price,
        /** InPost offer ID (from API response or when updating). */
        public ?string $inpostOfferId = null,
        /** GPSR info – EU product safety regulation (optional). */
        public ?GpsrInfoDto $gpsr = null,
        /** Shipping time – number of days to ship the parcel. */
        public ?ShippingTimeDto $shippingTime = null,
        /** Product URL in seller's store (max 2048 chars). */
        public ?string $affiliationProductUrl = null,
        /** Returns and complaints policy. */
        public ?PostSaleDto $postSale = null,
        /** Offer features (e.g. refundable). */
        public ?FeaturesDto $features = null,
        /** Offer status from InPost API (e.g. PENDING, PUBLISHED, REJECTED – only in response). */
        public ?string $status = null,
    ) {
    }

    /** @return array<string, mixed> Payload compatible with OfferProposal (POST/PATCH) */
    public function toArray(): array
    {
        $payload = [
            'externalId' => $this->externalId,
            'product' => $this->product->toArray(),
            'stock' => $this->stock->toArray(),
            'price' => $this->price->toArray(),
            'gpsr' => $this->gpsr?->toArray(),
            'shippingTime' => $this->shippingTime?->toArray(),
            'affiliationProductUrl' => !empty($this->affiliationProductUrl) ? $this->affiliationProductUrl : null,
            'postSale' => $this->postSale?->toArray(),
            'features' => $this->features?->toArray(),
        ];

        return array_filter($payload, static fn (mixed $v): bool => !empty($v));
    }
}
