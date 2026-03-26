<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Order\Response\OrderEventDto;
use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<OrderEventDto>
 */
final class OrderEventCollection extends AbstractCollection
{
    public function getType(): string
    {
        return OrderEventDto::class;
    }

    /**
     * @param list<OrderEventDto> $events
     */
    public static function fromArray(array $events): self
    {
        return new self($events);
    }
}
