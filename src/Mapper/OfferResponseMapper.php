<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper;

use malpka32\InPostBuySdk\Collection\OfferCollection;
use malpka32\InPostBuySdk\Dto\OfferDto;
use malpka32\InPostBuySdk\Dto\PriceDto;
use malpka32\InPostBuySdk\Dto\ProductDto;
use malpka32\InPostBuySdk\Dto\StockDto;

/**
 * Maps API response (Offers – List Offers) to OfferCollection.
 */
final class OfferResponseMapper implements ResponseMapperInterface
{
    public function __construct(
        private readonly DimensionMapper $dimensionMapper,
        private readonly AttributeValueMapper $attributeValueMapper,
    ) {
    }

    public function map(array $data): OfferCollection
    {
        $collection = new OfferCollection();
        $list = ArrayHelper::getList($data, ['data', 'items', 'offers']);
        foreach ($list as $item) {
            $offerData = ArrayHelper::extractOffer($item);
            if ($offerData !== []) {
                $collection->add($this->mapItem($offerData));
            }
        }
        return $collection;
    }

    /**
     * @param array<string, mixed> $offer
     */
    public function mapItem(array $offer): OfferDto
    {
        $productRaw = $offer['product'] ?? null;
        $stockRaw = $offer['stock'] ?? null;
        $priceRaw = $offer['price'] ?? null;
        $product = is_array($productRaw) ? $productRaw : [];
        /** @var array<string, mixed> $product */
        $stock = is_array($stockRaw) ? $stockRaw : [];
        /** @var array<string, mixed> $stock */
        $price = is_array($priceRaw) ? $priceRaw : [];
        /** @var array<string, mixed> $price */
        $grossPriceRaw = $price['grossPrice'] ?? null;
        $grossPrice = is_array($grossPriceRaw) ? $grossPriceRaw : [];
        /** @var array<string, mixed> $grossPrice */

        $attrsRaw = $product['attributes'] ?? null;
        $attributesList = ($attrsRaw !== null && is_array($attrsRaw)) ? array_values(array_filter($attrsRaw, 'is_array')) : null;
        /** @var list<array<string, mixed>>|null $attributesList */
        $attributes = $this->attributeValueMapper->map($attributesList);

        $dimRaw = $product['dimension'] ?? null;
        $dimInput = ($dimRaw !== null && is_array($dimRaw)) ? $dimRaw : null;
        /** @var array<string, mixed>|null $dimInput */
        $dimension = $this->dimensionMapper->map($dimInput);

        $externalId = ArrayHelper::asString(ArrayHelper::get($offer, 'externalId') ?? ArrayHelper::get($product, 'sku') ?? '');
        $quantity = ArrayHelper::asInt($stock['quantity'] ?? 0);
        $unit = ArrayHelper::asString(ArrayHelper::get($stock, 'unit') ?? 'UNIT');
        $amount = ArrayHelper::asFloat($grossPrice['amount'] ?? 0.0);
        $currency = ArrayHelper::asString(ArrayHelper::get($grossPrice, 'currency') ?? 'PLN');
        $taxRateInfo = ArrayHelper::asString(ArrayHelper::get($price, 'taxRateInfo') ?? '23%');

        $sku = ArrayHelper::get($product, 'sku');
        $ean = ArrayHelper::get($product, 'ean');
        $productDto = new ProductDto(
            name: ArrayHelper::asString(ArrayHelper::get($product, 'name') ?? ''),
            description: ArrayHelper::asString(ArrayHelper::get($product, 'description') ?? ''),
            brand: ArrayHelper::asString(ArrayHelper::get($product, 'brand') ?? ''),
            categoryId: ArrayHelper::asString(ArrayHelper::get($product, 'categoryId') ?? ''),
            sku: $sku === null ? null : ArrayHelper::asString($sku),
            ean: $ean === null ? null : ArrayHelper::asString($ean),
            attributes: $attributes,
            dimension: $dimension,
        );

        $stockDto = new StockDto(quantity: $quantity, unit: $unit);
        $priceDto = new PriceDto(amount: $amount, currency: $currency, taxRateInfo: $taxRateInfo);

        $inpostOfferId = ArrayHelper::get($offer, ['id', 'offerId']);
        return new OfferDto(
            externalId: $externalId,
            product: $productDto,
            stock: $stockDto,
            price: $priceDto,
            inpostOfferId: $inpostOfferId !== null ? ArrayHelper::asString($inpostOfferId) : null,
        );
    }
}
