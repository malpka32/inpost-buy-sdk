<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Category;

use malpka32\InPostBuySdk\Dto\Category\CategoryDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements ItemMapperInterface<CategoryDto>
 */
final class CategoryDtoMapper implements ItemMapperInterface
{
    public function canProcess(array $item): bool
    {
        return array_key_exists('id', $item) || array_key_exists('name', $item);
    }

    public function mapItem(mixed $item): CategoryDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        $id = ArrayHelper::get($item, 'id');
        $name = ArrayHelper::get($item, 'name');
        $parentId = ArrayHelper::get($item, ['parent_id', 'parentId']);
        if ($parentId === null) {
            $relations = $item['relations'] ?? null;
            if (is_array($relations)) {
                foreach ($relations as $relation) {
                    if (is_array($relation) && ($relation['relation'] ?? '') === 'MAIN_PARENT' && isset($relation['id'])) {
                        $parentId = $relation['id'];
                        break;
                    }
                }
            }
        }

        return new CategoryDto(
            id: $id === null ? null : ArrayHelper::asString($id),
            name: $name === null ? null : ArrayHelper::asString($name),
            parentId: $parentId !== null ? ArrayHelper::asString($parentId) : null,
        );
    }
}
