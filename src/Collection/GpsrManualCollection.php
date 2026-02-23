<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Offer\Gpsr\GpsrManualDto;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of GPSR manuals (instruction documents).
 *
 * @extends AbstractCollection<GpsrManualDto>
 */
final class GpsrManualCollection extends AbstractCollection
{
    public function getType(): string
    {
        return GpsrManualDto::class;
    }

    /**
     * @param list<GpsrManualDto> $items
     */
    public static function fromArray(array $items): self
    {
        return new self($items);
    }
}
