<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Offer\Command\OfferStockUpdateDto;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of offer stock updates (Batch Update Offer Stock payload).
 *
 * @extends AbstractCollection<OfferStockUpdateDto>
 */
final class OfferStockUpdateCollection extends AbstractCollection
{
    public function getType(): string
    {
        return OfferStockUpdateDto::class;
    }

    /**
     * @param OfferStockUpdateDto ...$updates
     */
    public static function fromUpdates(OfferStockUpdateDto ...$updates): self
    {
        return new self($updates);
    }
}
