<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper;

/**
 * Contract for mapping one raw object payload into nullable DTO.
 *
 * @template TDto of object
 */
interface SingleItemMapperInterface
{
    /**
     * @param mixed $data Raw API payload or null – implementor returns null for invalid input
     * @return TDto|null
     */
    public function map(mixed $data): ?object;
}
