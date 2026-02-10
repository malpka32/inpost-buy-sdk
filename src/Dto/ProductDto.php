<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

use malpka32\InPostBuySdk\Collection\AttributeValueCollection;

/**
 * Offer product data (OpenAPI: ProductInfoProposal).
 * name, description, brand, categoryId – required.
 */
final class ProductDto
{
    public function __construct(
        public string $name,
        public string $description,
        public string $brand,
        public string $categoryId,
        public ?string $sku = null,
        public ?string $ean = null,
        ?AttributeValueCollection $attributes = null,
        public ?DimensionDto $dimension = null,
    ) {
        $this->attributes = $attributes ?? new AttributeValueCollection();
    }

    public AttributeValueCollection $attributes;

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $product = [
            'name' => $this->name,
            'description' => $this->description,
            'brand' => $this->brand,
            'categoryId' => $this->categoryId,
        ];
        if ($this->sku !== null && $this->sku !== '') {
            $product['sku'] = $this->sku;
        }
        if ($this->ean !== null && $this->ean !== '') {
            $product['ean'] = $this->ean;
        }
        if (!$this->attributes->isEmpty()) {
            $product['attributes'] = [];
            foreach ($this->attributes as $attr) {
                $item = ['id' => $attr->id, 'values' => $attr->values];
                if ($attr->lang !== null && $attr->lang !== '') {
                    $item['lang'] = $attr->lang;
                }
                $product['attributes'][] = $item;
            }
        }
        if ($this->dimension !== null) {
            $product['dimension'] = $this->dimension->toArray();
        }
        return $product;
    }
}
