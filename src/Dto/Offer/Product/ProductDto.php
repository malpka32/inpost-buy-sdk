<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Product;

use malpka32\InPostBuySdk\Collection\AttributeValueCollection;

/**
 * Dane produktu oferty (OpenAPI: ProductInfoProposal).
 *
 * Propozycja produktu dopasowywanego do katalogu InPost Buy.
 * Required fields: name, description, brand, categoryId.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class ProductDto
{
    public function __construct(
        /** Nazwa produktu (ProductName). */
        public string $name,
        /** Opis produktu (ProductDescription). */
        public string $description,
        /** Marka produktu (ProductBrand). */
        public string $brand,
        /** UUID kategorii z drzewa kategorii InPost Buy (UuidIdentifier). */
        public string $categoryId,
        /** SKU – stock keeping unit (optional). */
        public ?string $sku = null,
        /** Product EAN code (optional). */
        public ?string $ean = null,
        ?AttributeValueCollection $attributes = null,
        /** Wymiary opakowania: szerokość, wysokość, długość (mm), waga (g). */
        public ?DimensionDto $dimension = null,
        /** Product model – e.g. "Basic V-neck" (optional). */
        public ?string $model = null,
        /** Parent model – e.g. "T-Shirt" (optional). */
        public ?string $superModel = null,
        /** Manufacturer part number (MPN) (optional). */
        public ?string $manufacturerProductNumber = null,
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
        if (!empty($this->sku)) {
            $product['sku'] = $this->sku;
        }
        if (!empty($this->ean)) {
            $product['ean'] = $this->ean;
        }
        if (!empty($this->model)) {
            $product['model'] = $this->model;
        }
        if (!empty($this->superModel)) {
            $product['superModel'] = $this->superModel;
        }
        if (!empty($this->manufacturerProductNumber)) {
            $product['manufacturerProductNumber'] = $this->manufacturerProductNumber;
        }
        if (!$this->attributes->isEmpty()) {
            $product['attributes'] = [];
            foreach ($this->attributes as $attr) {
                $item = ['id' => $attr->id, 'values' => $attr->values];
                if (!empty($attr->lang)) {
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
