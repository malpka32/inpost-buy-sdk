<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Offer\Deposit\DepositPositionDto;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of deposit positions used in offer price.
 *
 * @extends AbstractCollection<DepositPositionDto>
 */
final class DepositPositionCollection extends AbstractCollection
{
    public function getType(): string
    {
        return DepositPositionDto::class;
    }

    /**
     * @param list<DepositPositionDto> $items
     */
    public static function fromArray(array $items): self
    {
        return new self($items);
    }
}
