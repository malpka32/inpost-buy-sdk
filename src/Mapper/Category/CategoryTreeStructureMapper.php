<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Category;

use malpka32\InPostBuySdk\Collection\CategoryTreeCollection;
use malpka32\InPostBuySdk\Dto\Category\CategoryTreeNode;
use malpka32\InPostBuySdk\Helper\ArrayHelper;

/**
 * Maps API tree response (root array with nested children) to CategoryTreeCollection.
 * InPost API returns: [{"id":"...","name":"...","leaf":bool,"children":[...]}, ...]
 */
final class CategoryTreeStructureMapper
{
    /**
     * @param list<array<string, mixed>> $nodes Root-level category nodes
     * @return list<CategoryTreeNode>
     */
    private function mapNodes(array $nodes, ?string $parentId = null): array
    {
        $result = [];
        foreach ($nodes as $node) {
            $id = ArrayHelper::get($node, 'id');
            $id = $id !== null ? ArrayHelper::asString($id) : null;
            $name = ArrayHelper::get($node, 'name');
            $name = $name !== null ? ArrayHelper::asString($name) : null;
            $childrenRaw = $node['children'] ?? [];
            $children = is_array($childrenRaw)
                ? $this->mapNodes(array_values(array_filter($childrenRaw, 'is_array')), $id)
                : [];

            $result[] = new CategoryTreeNode($id, $name, $parentId, $children);
        }
        return $result;
    }

    public function map(array $data): CategoryTreeCollection
    {
        $nodes = array_values(array_filter(is_array($data) ? $data : [], 'is_array'));
        $mapped = $this->mapNodes($nodes, null);

        return CategoryTreeCollection::fromArray($mapped);
    }
}
