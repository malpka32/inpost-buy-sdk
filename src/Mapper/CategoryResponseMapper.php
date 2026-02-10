<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper;

use malpka32\InPostBuySdk\Dto\CategoryDto;

/**
 * Mapuje odpowiedź API na listę CategoryDto.
 */
final class CategoryResponseMapper
{
    /**
     * @return CategoryDto[]
     */
    public function map(array $data): array
    {
        $list = $data['items'] ?? $data['categories'] ?? (isset($data['id']) ? [$data] : $data);
        if (!is_array($list)) {
            return [];
        }
        $out = [];
        foreach ($list as $item) {
            if (!is_array($item)) {
                continue;
            }
            $out[] = new CategoryDto(
                id: $item['id'] ?? null,
                name: $item['name'] ?? null,
                parentId: $item['parent_id'] ?? $item['parentId'] ?? null,
            );
        }
        return $out;
    }
}
