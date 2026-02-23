<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Offer\Deposit\DepositLabelDto;
use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<DepositLabelDto>
 */
final class DepositLabelCollection extends AbstractCollection
{
    public function getType(): string
    {
        return \malpka32\InPostBuySdk\Dto\Offer\Deposit\DepositLabelDto::class;
    }
}
