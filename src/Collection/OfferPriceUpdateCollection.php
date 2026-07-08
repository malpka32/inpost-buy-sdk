<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Offer\Command\OfferPriceUpdateDto;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of offer price updates (Batch Update Offer Price payload).
 *
 * @extends AbstractCollection<OfferPriceUpdateDto>
 */
final class OfferPriceUpdateCollection extends AbstractCollection
{
    public function getType(): string
    {
        return OfferPriceUpdateDto::class;
    }

    /**
     * @param OfferPriceUpdateDto ...$updates
     */
    public static function fromUpdates(OfferPriceUpdateDto ...$updates): self
    {
        return new self($updates);
    }
}
