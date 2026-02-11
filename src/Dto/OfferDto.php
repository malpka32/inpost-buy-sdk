<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * Offer – data for create/update (OpenAPI: OfferProposal) or from API response.
 * externalId – external identifier (e.g. SKU), set by the developer.
 * inpostOfferId – InPost offer ID (from API response or for update).
 */
final class OfferDto
{
    public function __construct(
        public string $externalId,
        public ProductDto $product,
        public StockDto $stock,
        public PriceDto $price,
        public ?string $inpostOfferId = null,
    ) {
    }

    /** @return array<string, mixed> Payload OfferProposal */
    public function toArray(): array
    {
        return [
            'externalId' => $this->externalId,
            'product' => $this->product->toArray(),
            'stock' => $this->stock->toArray(),
            'price' => $this->price->toArray(),
        ];
    }
}
