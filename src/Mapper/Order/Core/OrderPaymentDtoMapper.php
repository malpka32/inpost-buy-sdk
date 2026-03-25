<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Core;

use malpka32\InPostBuySdk\Dto\Order\Core\OrderPaymentDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements ItemMapperInterface<OrderPaymentDto>
 */
final class OrderPaymentDtoMapper implements ItemMapperInterface
{
    public function canProcess(array $item): bool
    {
        return isset($item['paymentId'])
            || isset($item['paymentType'])
            || isset($item['paymentDate']);
    }

    public function mapItem(mixed $item): OrderPaymentDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        $paymentId = ArrayHelper::get($item, 'paymentId');
        $paymentType = ArrayHelper::get($item, 'paymentType');
        $paymentDate = self::parseDateTime(ArrayHelper::get($item, 'paymentDate'));

        return new OrderPaymentDto(
            paymentId: $paymentId === null ? null : ArrayHelper::asString($paymentId),
            paymentType: $paymentType === null ? null : ArrayHelper::asString($paymentType),
            paymentDate: $paymentDate,
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
