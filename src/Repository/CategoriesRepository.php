<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Repository;

use malpka32\InPostBuySdk\Api\CategoriesEndpointInterface;
use malpka32\InPostBuySdk\Collection\AttributeDefinitionCollection;
use malpka32\InPostBuySdk\Collection\CategoryTreeCollection;
use malpka32\InPostBuySdk\Dto\Category\CategoryDetailedDto;
use malpka32\InPostBuySdk\Mapper\Attribute\AttributeDefinitionMapper;
use malpka32\InPostBuySdk\Mapper\Category\CategoryDetailedMapper;
use malpka32\InPostBuySdk\Mapper\Category\CategoryTreeStructureMapper;

/**
 * Categories repository – fetches category tree from API (same format as response).
 */
final class CategoriesRepository
{
    public function __construct(
        private readonly CategoriesEndpointInterface $endpoint,
        private readonly CategoryTreeStructureMapper $treeMapper,
        private readonly CategoryDetailedMapper $categoryDetailedMapper,
        private readonly AttributeDefinitionMapper $attributeDefinitionMapper,
    ) {
    }

    /**
     * Fetches category tree – hierarchy [{"id","name","children":[...]}, ...].
     *
     * @param string|null $categoryId Subtree root (null = root)
     * @param int|null    $depth      Depth 0–4 (null = API default)
     */
    public function getCategories(?string $categoryId = null, ?int $depth = null): CategoryTreeCollection
    {
        $data = $this->endpoint->fetch($categoryId, $depth);

        return $this->treeMapper->map($data);
    }

    public function getCategory(string $categoryId, ?int $depth = null): CategoryDetailedDto
    {
        $data = $this->endpoint->get($categoryId, $depth);
        $dto = $this->categoryDetailedMapper->map($data);
        if ($dto === null) {
            throw new \RuntimeException('Category response could not be mapped to CategoryDetailedDto.');
        }

        return $dto;
    }

    public function getCategoryAttributes(string $categoryId): AttributeDefinitionCollection
    {
        $data = $this->endpoint->getAttributes($categoryId);

        return $this->attributeDefinitionMapper->map($data);
    }
}
