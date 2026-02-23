<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order;

use malpka32\InPostBuySdk\Dto\Order\OrderDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements ItemMapperInterface<OrderDto>
 */
final class OrderDtoMapper implements ItemMapperInterface
{
    public function canProcess(array $item): bool
    {
        return array_key_exists('id', $item) || array_key_exists('order_id', $item);
    }

    public function mapItem(mixed $item): OrderDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
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
