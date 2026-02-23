<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Category;

use malpka32\InPostBuySdk\Dto\Category\CategoryDetailedDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<CategoryDetailedDto>
 */
final class CategoryDetailedMapper implements SingleItemMapperInterface
{
    /**
     * @param array<string, mixed> $data Category object from API (id, leaf, name, description, relations, metadata, children)
     */
    public function map(mixed $data): ?CategoryDetailedDto
    {
        if (!is_array($data) || $data === []) {
            return null;
        }
        /** @var array<string, mixed> $data */
        $children = null;
        $childrenRaw = $data['children'] ?? null;
        if (is_array($childrenRaw) && $childrenRaw !== []) {
            $children = [];
            foreach ($childrenRaw as $c) {
                if (!is_array($c)) {
                    continue;
                }
                $mapped = $this->map($c);
                if ($mapped !== null) {
                    $children[] = $mapped;
                }
            }
        }

        $relations = null;
        $relationsRaw = $data['relations'] ?? null;
        if (is_array($relationsRaw) && $relationsRaw !== []) {
            $relations = array_values(array_filter($relationsRaw, 'is_array'));
            /** @var list<array<string, mixed>> $relations */
        }

        $metadata = null;
        if (isset($data['metadata']) && is_array($data['metadata'])) {
            $metadata = $data['metadata'];
            /** @var array<string, mixed> $metadata */
        }

        return new CategoryDetailedDto(
            id: ArrayHelper::asString($data['id'] ?? ''),
            leaf: (bool) ($data['leaf'] ?? false),
            name: ArrayHelper::asString($data['name'] ?? ''),
            description: ArrayHelper::asString($data['description'] ?? ''),
            doesNotRequireGpsrInfo: (bool) ($data['doesNotRequireGpsrInfo'] ?? false),
            relations: $relations,
            metadata: $metadata,
            children: $children,
        );
    }
}
