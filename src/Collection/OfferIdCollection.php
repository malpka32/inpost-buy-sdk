<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use Ramsey\Collection\AbstractCollection;

/**
 * Collection of offer IDs (putOffers result).
 *
 * @extends AbstractCollection<string>
 */
final class OfferIdCollection extends AbstractCollection
{
    public function getType(): string
    {
        return 'string';
    }

    /**
     * @param string ...$ids
     */
    public static function fromIds(string ...$ids): self
    {
        return new self($ids);
    }

    /**
     * @param list<string> $ids
     */
    public static function fromArray(array $ids): self
    {
        return new self(array_map('strval', $ids));
    }
}
