<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper;

use malpka32\InPostBuySdk\Collection\OrderCollection;
use malpka32\InPostBuySdk\Dto\OrderDto;

/**
 * Maps API response to OrderCollection / OrderDto.
 */
final class OrderResponseMapper implements ResponseMapperInterface
{
    public function map(array $data): OrderCollection
    {
        $collection = new OrderCollection();
        $list = ArrayHelper::getList($data, ['items', 'orders']);
        foreach ($list as $item) {
            $collection->add($this->mapItem($item));
        }
        return $collection;
    }

    public function mapItem(array $item): OrderDto
    {
        $createdAt = self::parseDateTime(ArrayHelper::get($item, ['created_at', 'createdAt']));
        $updatedAt = self::parseDateTime(ArrayHelper::get($item, ['updated_at', 'updatedAt']));

        return new OrderDto(
            inpostOrderId: (string) ArrayHelper::get($item, ['id', 'order_id'], ''),
            status: ArrayHelper::get($item, 'status'),
            reference: ArrayHelper::get($item, 'reference'),
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            items: ArrayHelper::get($item, 'items'),
            raw: $item,
        );
    }

    private static function parseDateTime(mixed $value): ?\DateTimeInterface
    {
        if ($value === null || $value === '') {
            return null;
        }
        $str = (string) $value;
        $parsed = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $str);
        return $parsed ?: null;
    }
}
