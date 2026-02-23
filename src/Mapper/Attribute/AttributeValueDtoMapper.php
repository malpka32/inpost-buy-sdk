<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Attribute;

use malpka32\InPostBuySdk\Dto\Attribute\AttributeValueDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * Maps raw API AttributeValue item to AttributeValueDto.
 * Uses constructor-based creation (no static factory on DTO).
 */
/**
 * @implements ItemMapperInterface<AttributeValueDto>
 */
final class AttributeValueDtoMapper implements ItemMapperInterface
{
    public function canProcess(array $item): bool
    {
        return !empty($item['id']) && isset($item['values']);
    }

    public function mapItem(mixed $item): AttributeValueDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        $rawValues = $item['values'] ?? null;
        $values = is_array($rawValues)
            ? array_values(array_map(fn (mixed $v): string => ArrayHelper::asString($v), $rawValues))
            : [];

        return new AttributeValueDto(
            id: ArrayHelper::asString($item['id'] ?? ''),
            values: $values,
            lang: isset($item['lang']) ? ArrayHelper::asString($item['lang']) : null,
        );
    }
}
