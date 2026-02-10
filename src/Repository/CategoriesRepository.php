<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Repository;

use malpka32\InPostBuySdk\Api\CategoriesEndpointInterface;
use malpka32\InPostBuySdk\Collection\CategoryCollection;
use malpka32\InPostBuySdk\Mapper\CategoryResponseMapper;

/**
 * Categories repository – endpoint + mapping → DTO.
 */
final class CategoriesRepository
{
    public function __construct(
        private readonly CategoriesEndpointInterface $endpoint,
        private readonly CategoryResponseMapper $mapper,
    ) {
    }

    public function getCategories(): CategoryCollection
    {
        $data = $this->endpoint->fetch();
        return $this->mapper->map($data);
    }
}
