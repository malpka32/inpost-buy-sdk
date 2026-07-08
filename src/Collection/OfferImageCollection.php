<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Offer\Image\OfferImageDto;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of offer images.
 *
 * @extends AbstractCollection<OfferImageDto>
 */
final class OfferImageCollection extends AbstractCollection
{
    public function getType(): string
    {
        return OfferImageDto::class;
    }

    /**
     * @param list<OfferImageDto> $items
     */
    public static function fromArray(array $items): self
    {
        return new self($items);
    }
}
