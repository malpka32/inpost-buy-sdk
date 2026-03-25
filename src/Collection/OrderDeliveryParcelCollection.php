<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Order\Core\OrderDeliveryParcelDto;
use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<OrderDeliveryParcelDto>
 */
final class OrderDeliveryParcelCollection extends AbstractCollection
{
    public function getType(): string
    {
        return OrderDeliveryParcelDto::class;
    }
}
