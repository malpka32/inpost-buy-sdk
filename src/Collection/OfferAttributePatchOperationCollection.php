<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Offer\Command\OfferAttributePatchOperationDto;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of Offer attribute patch operations (Patch Offer attributes payload).
 *
 * @extends AbstractCollection<OfferAttributePatchOperationDto>
 */
final class OfferAttributePatchOperationCollection extends AbstractCollection
{
    public function getType(): string
    {
        return OfferAttributePatchOperationDto::class;
    }

    /**
     * @param OfferAttributePatchOperationDto ...$operations
     */
    public static function fromOperations(OfferAttributePatchOperationDto ...$operations): self
    {
        return new self($operations);
    }
}
