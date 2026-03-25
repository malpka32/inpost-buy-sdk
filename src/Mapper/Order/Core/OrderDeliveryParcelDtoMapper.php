<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Core;

use malpka32\InPostBuySdk\Dto\Order\Core\OrderDeliveryParcelDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements ItemMapperInterface<OrderDeliveryParcelDto>
 */
final class OrderDeliveryParcelDtoMapper implements ItemMapperInterface
{
    public function canProcess(array $item): bool
    {
        return isset($item['trackingNumber'])
            || isset($item['status'])
            || isset($item['createdAt']);
    }

    public function mapItem(mixed $item): OrderDeliveryParcelDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        $trackingNumber = ArrayHelper::get($item, 'trackingNumber');
        $status = ArrayHelper::get($item, 'status');
        $createdAt = self::parseDateTime(ArrayHelper::get($item, 'createdAt'));

        return new OrderDeliveryParcelDto(
            trackingNumber: $trackingNumber === null ? null : ArrayHelper::asString($trackingNumber),
            createdAt: $createdAt,
            status: $status === null ? null : ArrayHelper::asString($status),
        );
    }

    private static function parseDateTime(mixed $value): ?\DateTimeInterface
    {
        if ($value === null || $value === '') {
            return null;
        }
        $str = ArrayHelper::asString($value);
        $parsed = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $str);
        if ($parsed instanceof \DateTimeInterface) {
            return $parsed;
        }
        try {
            return new \DateTimeImmutable($str);
        } catch (\Exception) {
            return null;
        }
    }
}
