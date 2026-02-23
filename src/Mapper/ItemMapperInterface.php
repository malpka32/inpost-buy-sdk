<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper;

/**
 * Contract for mapping one raw API item into one DTO.
 *
 * @template TDto of object
 */
interface ItemMapperInterface
{
    /**
     * Checks if given raw payload can be mapped safely.
     *
     * @param array<string, mixed> $item
     */
    public function canProcess(array $item): bool;

    /**
     * Maps one raw item to one DTO.
     * Accepts mixed (null, non-array) – implementor normalizes to array (e.g. [] for invalid input).
     *
     * @param mixed $item Raw API payload or null
     * @return TDto
     */
    public function mapItem(mixed $item): object;
}
