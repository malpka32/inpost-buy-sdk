<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Offer\Command\OfferCommandResultDto;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of offer command results (batch price/stock update responses).
 *
 * @extends AbstractCollection<OfferCommandResultDto>
 */
final class OfferCommandResultCollection extends AbstractCollection
{
    public function getType(): string
    {
        return OfferCommandResultDto::class;
    }

    /**
     * @param OfferCommandResultDto ...$results
     */
    public static function fromResults(OfferCommandResultDto ...$results): self
    {
        return new self($results);
    }
}
