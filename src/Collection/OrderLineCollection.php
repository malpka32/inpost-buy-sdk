<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Order\Line\OrderLineDto;
use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<OrderLineDto>
 */
final class OrderLineCollection extends AbstractCollection
{
    public function getType(): string
    {
        return OrderLineDto::class;
    }

    /**
     * @param list<OrderLineDto> $lines
     */
    public static function fromArray(array $lines): self
    {
        return new self($lines);
    }
}
