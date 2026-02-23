<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Core;

use malpka32\InPostBuySdk\Collection\AttributeValueCollection;
use malpka32\InPostBuySdk\Dto\Offer\Product\ProductDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\Attribute\AttributeValueCollectionMapper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements ItemMapperInterface<ProductDto>
 */
final class OfferProductDtoMapper implements ItemMapperInterface
{
    public function __construct(
        private readonly DimensionMapper $dimensionMapper = new DimensionMapper(),
        private readonly AttributeValueCollectionMapper $attributeValueMapper = new AttributeValueCollectionMapper(),
    ) {
    }

    public function canProcess(array $item): bool
    {
        return array_key_exists('name', $item) || array_key_exists('categoryId', $item);
    }

    public function mapItem(mixed $item): ProductDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        $attributes = $this->mapAttributes($item);
        $dimension = $this->mapDimension($item);

        $sku = ArrayHelper::get($item, 'sku');
        $ean = ArrayHelper::get($item, 'ean');
        $model = ArrayHelper::get($item, 'model');
        $superModel = ArrayHelper::get($item, 'superModel');
        $mpn = ArrayHelper::get($item, 'manufacturerProductNumber');

        return new ProductDto(
            name: ArrayHelper::asString(ArrayHelper::get($item, 'name') ?? ''),
            description: ArrayHelper::asString(ArrayHelper::get($item, 'description') ?? ''),
            brand: ArrayHelper::asString(ArrayHelper::get($item, 'brand') ?? ''),
            categoryId: ArrayHelper::asString(ArrayHelper::get($item, 'categoryId') ?? ''),
            sku: $sku === null ? null : ArrayHelper::asString($sku),
            ean: $ean === null ? null : ArrayHelper::asString($ean),
            attributes: $attributes,
            dimension: $dimension,
            model: $model === null ? null : ArrayHelper::asString($model),
            superModel: $superModel === null ? null : ArrayHelper::asString($superModel),
            manufacturerProductNumber: $mpn === null ? null : ArrayHelper::asString($mpn),
        );
    }

    /**
     * @param array<string, mixed> $product
     */
    private function mapAttributes(array $product): AttributeValueCollection
    {
        $attrsRaw = $product['attributes'] ?? null;
        $attributesList = ($attrsRaw !== null && is_array($attrsRaw)) ? array_values(array_filter($attrsRaw, 'is_array')) : [];
        /** @var list<array<string, mixed>> $attributesList */
        return $this->attributeValueMapper->map($attributesList);
    }

    /**
     * @param array<string, mixed> $product
     */
    private function mapDimension(array $product): ?\malpka32\InPostBuySdk\Dto\Offer\Product\DimensionDto
    {
        $dimRaw = $product['dimension'] ?? null;
        $dimInput = ($dimRaw !== null && is_array($dimRaw)) ? $dimRaw : null;
        /** @var array<string, mixed>|null $dimInput */
        return $this->dimensionMapper->map($dimInput);
    }
}
