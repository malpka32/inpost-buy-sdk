<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Builder;

use malpka32\InPostBuySdk\Collection\CategoryCollection;
use malpka32\InPostBuySdk\Collection\CategoryTreeCollection;
use malpka32\InPostBuySdk\Dto\Category\CategoryTreeNode;

/**
 * Builds category tree from flat CategoryCollection (in-memory, no API call).
 */
final class CategoryTreeBuilder
{
    /**
     * Root nodes (parentId === null), each with nested children.
     */
    public function build(CategoryCollection $flat): CategoryTreeCollection
    {
        $byId = [];
        $childIdsByParentId = [];
        foreach ($flat as $dto) {
            if ($dto->id === null) {
                continue;
            }
            $byId[$dto->id] = $dto;
            $parentId = $dto->parentId ?? '';
            if (!empty($parentId)) {
                $childIdsByParentId[$parentId] = $childIdsByParentId[$parentId] ?? [];
                $childIdsByParentId[$parentId][] = $dto->id;
            }
        }

        $rootIds = [];
        foreach ($flat as $dto) {
            if ($dto->id === null) {
                continue;
            }
            $parentId = $dto->parentId ?? '';
            if ($parentId === '' || !isset($byId[$parentId])) {
                $rootIds[] = $dto->id;
            }
        }

        $buildNode = function (string $id) use (&$buildNode, $byId, $childIdsByParentId): CategoryTreeNode {
            $dto = $byId[$id];
            $childIds = $childIdsByParentId[$id] ?? [];
            $children = array_map($buildNode, $childIds);
            return new CategoryTreeNode(
                $dto->id,
                $dto->name,
                $dto->parentId,
                $children
            );
        };

        $roots = array_map($buildNode, $rootIds);

        return CategoryTreeCollection::fromArray($roots);
    }
}
