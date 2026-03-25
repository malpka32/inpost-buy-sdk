<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Order\Core\OrderPaymentDto;
use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<OrderPaymentDto>
 */
final class OrderPaymentCollection extends AbstractCollection
{
    public function getType(): string
    {
        return OrderPaymentDto::class;
    }
}
