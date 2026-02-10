<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

final class OfferDto
{
    /**
     * @param list<AttributeValueDto>|null $attributes
     */
    public function __construct(
        public ?string $inpostOfferId = null,
        public ?int $idProduct = null,
        public ?int $idProductAttribute = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?string $sku = null,
        public ?string $ean = null,
        public ?float $priceGross = null,
        public ?float $quantity = null,
        public ?string $inpostCategoryId = null,
        public ?array $imageUrls = null,
        public ?array $attributes = null,
        public ?DimensionDto $dimension = null,
    ) {
    }
}
