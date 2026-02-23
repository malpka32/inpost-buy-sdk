<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper;

/**
 * Contract for mapping raw API response arrays into typed collections.
 *
 * @template TCollection of object
 */
interface CollectionMapperInterface
{
    /**
     * @param array<string, mixed>|list<mixed> $data
     * @return TCollection
     */
    public function map(array $data): object;
}
