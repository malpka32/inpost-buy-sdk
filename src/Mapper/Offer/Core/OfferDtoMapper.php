<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Core;

use malpka32\InPostBuySdk\Dto\Offer\OfferDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;
use malpka32\InPostBuySdk\Mapper\Offer\Gpsr\OfferGpsrSingleMapper;
use malpka32\InPostBuySdk\Mapper\Offer\PostSale\OfferFeaturesSingleMapper;
use malpka32\InPostBuySdk\Mapper\Offer\PostSale\OfferPostSaleSingleMapper;
use malpka32\InPostBuySdk\Mapper\Offer\PostSale\OfferShippingTimeSingleMapper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements ItemMapperInterface<OfferDto>
 */
final class OfferDtoMapper implements ItemMapperInterface
{
    public function __construct(
        /** @var ItemMapperInterface<\malpka32\InPostBuySdk\Dto\Offer\Product\ProductDto> */
        private readonly ItemMapperInterface $productMapper = new OfferProductDtoMapper(),
        /** @var ItemMapperInterface<\malpka32\InPostBuySdk\Dto\Offer\StockDto> */
        private readonly ItemMapperInterface $stockMapper = new OfferStockDtoMapper(),
        /** @var ItemMapperInterface<\malpka32\InPostBuySdk\Dto\Offer\PriceDto> */
        private readonly ItemMapperInterface $priceMapper = new OfferPriceDtoMapper(),
        /** @var SingleItemMapperInterface<\malpka32\InPostBuySdk\Dto\Offer\Gpsr\GpsrInfoDto> */
        private readonly SingleItemMapperInterface $gpsrMapper = new OfferGpsrSingleMapper(),
        /** @var SingleItemMapperInterface<\malpka32\InPostBuySdk\Dto\Offer\ShippingTimeDto> */
        private readonly SingleItemMapperInterface $shippingTimeMapper = new OfferShippingTimeSingleMapper(),
        /** @var SingleItemMapperInterface<\malpka32\InPostBuySdk\Dto\Offer\PostSaleDto> */
        private readonly SingleItemMapperInterface $postSaleMapper = new OfferPostSaleSingleMapper(),
        /** @var SingleItemMapperInterface<\malpka32\InPostBuySdk\Dto\Offer\FeaturesDto> */
        private readonly SingleItemMapperInterface $featuresMapper = new OfferFeaturesSingleMapper(),
    ) {
    }

    public function canProcess(array $item): bool
    {
        return isset($item['product']) && is_array($item['product']);
    }

    public function mapItem(mixed $offer): OfferDto
    {
        $offer = is_array($offer) ? $offer : [];
        /** @var array<string, mixed> $offer */

        $productDto = $this->productMapper->mapItem($offer['product'] ?? null);
        $stockDto = $this->stockMapper->mapItem($offer['stock'] ?? null);
        $priceDto = $this->priceMapper->mapItem($offer['price'] ?? null);
        $externalId = ArrayHelper::asString(ArrayHelper::get($offer, 'externalId') ?? $productDto->sku ?? '');
        $gpsr = $this->gpsrMapper->map($offer['gpsr'] ?? null);
        $shippingTime = $this->shippingTimeMapper->map($offer['shippingTime'] ?? null);
        $affiliationProductUrl = ArrayHelper::get($offer, 'affiliationProductUrl');
        $postSale = $this->postSaleMapper->map($offer['postSale'] ?? null);
        $features = $this->featuresMapper->map($offer['features'] ?? null);
        $inpostOfferId = ArrayHelper::get($offer, ['id', 'offerId']);
        $status = ArrayHelper::get($offer, ['status', 'offerStatus']);
        return new OfferDto(
            externalId: $externalId,
            product: $productDto,
            stock: $stockDto,
            price: $priceDto,
            inpostOfferId: $inpostOfferId !== null ? ArrayHelper::asString($inpostOfferId) : null,
            gpsr: $gpsr,
            shippingTime: $shippingTime,
            affiliationProductUrl: $affiliationProductUrl !== null ? ArrayHelper::asString($affiliationProductUrl) : null,
            postSale: $postSale,
            features: $features,
            status: $status !== null ? ArrayHelper::asString($status) : null,
        );
    }
}
