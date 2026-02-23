<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Offer\Response\OfferPutResultDto;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of offer put results (batch create response).
 *
 * @extends AbstractCollection<OfferPutResultDto>
 */
final class OfferPutResultCollection extends AbstractCollection
{
    public function getType(): string
    {
        return OfferPutResultDto::class;
    }

    /**
     * @param OfferPutResultDto ...$results
     */
    public static function fromResults(OfferPutResultDto ...$results): self
    {
        return new self($results);
    }
}
