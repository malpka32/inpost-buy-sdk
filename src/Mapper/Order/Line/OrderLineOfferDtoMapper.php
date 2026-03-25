<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Line;

use malpka32\InPostBuySdk\Dto\Order\Core\OrderMoneyDto;
use malpka32\InPostBuySdk\Dto\Order\Line\OrderLineOfferDto;
use malpka32\InPostBuySdk\Dto\Order\Line\OrderLineProductDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;
use malpka32\InPostBuySdk\Mapper\Order\Core\OrderMoneyDtoMapper;

/**
 * @implements SingleItemMapperInterface<OrderLineOfferDto>
 */
final class OrderLineOfferDtoMapper implements SingleItemMapperInterface
{
    public function __construct(
        private readonly SingleItemMapperInterface $productMapper = new OrderLineProductDtoMapper(),
        private readonly SingleItemMapperInterface $moneyMapper = new OrderMoneyDtoMapper(),
    ) {
    }

    public function map(mixed $data): ?OrderLineOfferDto
    {
        if (!is_array($data)) {
            return null;
        }

        $offerId = ArrayHelper::get($data, 'offerId');
        $externalId = ArrayHelper::get($data, 'externalId');

        $productRaw = ArrayHelper::get($data, 'product');
        $product = is_array($productRaw) ? $this->productMapper->map($productRaw) : null;

        $priceRaw = ArrayHelper::get($data, 'price');
        $basePriceRaw = ArrayHelper::get($data, 'basePrice');
        $promotionPriceRaw = ArrayHelper::get($data, 'promotionPrice');

        return new OrderLineOfferDto(
            offerId: $offerId === null ? null : ArrayHelper::asString($offerId),
            product: $product instanceof OrderLineProductDto ? $product : null,
            price: $this->mapMoney($priceRaw),
            basePrice: $this->mapMoney($basePriceRaw),
            promotionPrice: $this->mapMoney($promotionPriceRaw),
            externalId: $externalId === null ? null : ArrayHelper::asString($externalId),
        );
    }

    private function mapMoney(mixed $raw): ?OrderMoneyDto
    {
        $mapped = $this->moneyMapper->map($raw);

        return $mapped instanceof OrderMoneyDto ? $mapped : null;
    }
}
