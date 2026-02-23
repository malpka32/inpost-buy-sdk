<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Offer\OfferDto;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of offers for batch creation (putOffers).
 *
 * @extends AbstractCollection<OfferDto>
 */
final class OfferCollection extends AbstractCollection
{
    public function getType(): string
    {
        return \malpka32\InPostBuySdk\Dto\Offer\OfferDto::class;
    }

    /**
     * @param OfferDto ...$offers
     */
    public static function fromOffers(OfferDto ...$offers): self
    {
        return new self($offers);
    }

    /**
     * @param list<OfferDto> $offers
     */
    public static function fromArray(array $offers): self
    {
        return new self($offers);
    }
}
