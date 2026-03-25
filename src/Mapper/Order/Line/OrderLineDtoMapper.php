<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Line;

use malpka32\InPostBuySdk\Dto\Order\Line\OrderLineDto;
use malpka32\InPostBuySdk\Dto\Order\Line\OrderLineOfferDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements ItemMapperInterface<OrderLineDto>
 */
final class OrderLineDtoMapper implements ItemMapperInterface
{
    public function __construct(
        private readonly OrderLineOfferDtoMapper $offerMapper = new OrderLineOfferDtoMapper(),
    ) {
    }

    public function canProcess(array $item): bool
    {
        $payload = self::extractOfferPayload($item);

        return $payload !== [];
    }

    public function mapItem(mixed $item): OrderLineDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        $payload = self::extractOfferPayload($item);
        $offer = $this->offerMapper->map($payload);
        if (!$offer instanceof OrderLineOfferDto) {
            return new OrderLineDto(offer: new OrderLineOfferDto());
        }

        return new OrderLineDto(offer: $offer);
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    private static function extractOfferPayload(array $item): array
    {
        $wrapped = ArrayHelper::get($item, 'offer');
        if (is_array($wrapped)) {
            /** @var array<string, mixed> $wrapped */
            return $wrapped;
        }

        if (ArrayHelper::get($item, 'offerId') !== null
            || ArrayHelper::get($item, 'product') !== null
            || ArrayHelper::get($item, 'price') !== null) {
            return $item;
        }

        return [];
    }
}
