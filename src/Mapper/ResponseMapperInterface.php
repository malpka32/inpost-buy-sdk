<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper;

use Ramsey\Collection\CollectionInterface;

/**
 * API response mapper contract – maps to DTO collection.
 */
interface ResponseMapperInterface
{
    /**
     * Maps raw API response to DTO collection.
     *
     * @param array<string, mixed> $data
     * @return CollectionInterface<mixed>
     */
    public function map(array $data): CollectionInterface;
}
