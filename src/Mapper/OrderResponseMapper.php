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

    /**
     * @param array<string, mixed> $item
     */
    public function mapItem(array $item): OrderDto
    {
        $createdAt = self::parseDateTime(ArrayHelper::get($item, ['created_at', 'createdAt']));
        $updatedAt = self::parseDateTime(ArrayHelper::get($item, ['updated_at', 'updatedAt']));

        $status = ArrayHelper::get($item, 'status');
        $reference = ArrayHelper::get($item, 'reference');
        $itemsRaw = ArrayHelper::get($item, 'items');
        $items = null;
        if (is_array($itemsRaw)) {
            $filtered = array_values(array_filter($itemsRaw, 'is_array'));
            /** @var list<array<string, mixed>> $filtered */
            $items = $filtered;
        }

        return new OrderDto(
            inpostOrderId: ArrayHelper::asString(ArrayHelper::get($item, ['id', 'order_id'], '')),
            status: $status === null ? null : ArrayHelper::asString($status),
            reference: $reference === null ? null : ArrayHelper::asString($reference),
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            items: $items,
            raw: $item,
        );
    }

    private static function parseDateTime(mixed $value): ?\DateTimeInterface
    {
        if ($value === null || $value === '') {
            return null;
        }
        $str = ArrayHelper::asString($value);
        $parsed = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $str);
        return $parsed ?: null;
    }
}
