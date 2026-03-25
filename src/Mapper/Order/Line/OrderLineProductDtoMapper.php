<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Line;

use malpka32\InPostBuySdk\Dto\Order\Line\OrderLineProductDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<OrderLineProductDto>
 */
final class OrderLineProductDtoMapper implements SingleItemMapperInterface
{
    public function map(mixed $data): ?OrderLineProductDto
    {
        if (!is_array($data)) {
            return null;
        }

        $productId = ArrayHelper::get($data, 'productId');
        $name = ArrayHelper::get($data, 'name');
        $ean = ArrayHelper::get($data, 'ean');
        $sku = ArrayHelper::get($data, 'sku');

        return new OrderLineProductDto(
            productId: $productId === null ? null : ArrayHelper::asString($productId),
            name: $name === null ? null : ArrayHelper::asString($name),
            ean: $ean === null ? null : ArrayHelper::asString($ean),
            sku: $sku === null ? null : ArrayHelper::asString($sku),
        );
    }
}
