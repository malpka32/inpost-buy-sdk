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
            if (is_array($item)) {
                $offerData = ArrayHelper::extractOffer($item);
                if ($offerData !== []) {
                    $collection->add($this->mapItem($offerData));
                }
            }
        }
        return $collection;
    }

    /**
     * @param array<string, mixed> $offer
     */
    public function mapItem(array $offer): OfferDto
    {
        $product = $offer['product'] ?? [];
        $stock = $offer['stock'] ?? [];
        $price = $offer['price'] ?? [];
        $grossPrice = is_array($price['grossPrice'] ?? null) ? $price['grossPrice'] : [];

        $attributes = $this->attributeValueMapper->map($product['attributes'] ?? null);
        $dimension = $this->dimensionMapper->map($product['dimension'] ?? null);

        $externalId = (string) (ArrayHelper::get($offer, 'externalId') ?? ArrayHelper::get($product, 'sku') ?? '');
        $quantity = isset($stock['quantity']) ? (int) $stock['quantity'] : 0;
        $unit = (string) (ArrayHelper::get($stock, 'unit') ?? 'UNIT');
        $amount = isset($grossPrice['amount']) ? (float) $grossPrice['amount'] : 0.0;
        $currency = (string) (ArrayHelper::get($grossPrice, 'currency') ?? 'PLN');
        $taxRateInfo = (string) (ArrayHelper::get($price, 'taxRateInfo') ?? '23%');

        $productDto = new ProductDto(
            name: (string) (ArrayHelper::get($product, 'name') ?? ''),
            description: (string) (ArrayHelper::get($product, 'description') ?? ''),
            brand: (string) (ArrayHelper::get($product, 'brand') ?? ''),
            categoryId: (string) (ArrayHelper::get($product, 'categoryId') ?? ''),
            sku: ArrayHelper::get($product, 'sku'),
            ean: ArrayHelper::get($product, 'ean'),
            attributes: $attributes,
            dimension: $dimension,
        );

        $stockDto = new StockDto(quantity: $quantity, unit: $unit);
        $priceDto = new PriceDto(amount: $amount, currency: $currency, taxRateInfo: $taxRateInfo);

        return new OfferDto(
            externalId: $externalId,
            product: $productDto,
            stock: $stockDto,
            price: $priceDto,
            inpostOfferId: ArrayHelper::get($offer, ['id', 'offerId']),
        );
    }
}
