<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Attribute;

use malpka32\InPostBuySdk\Collection\AttributeDefinitionCollection;
use malpka32\InPostBuySdk\Dto\Attribute\AttributeDefinitionDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\CollectionMapperInterface;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements CollectionMapperInterface<AttributeDefinitionCollection>
 * @implements ItemMapperInterface<AttributeDefinitionDto>
 */
final class AttributeDefinitionMapper implements CollectionMapperInterface, ItemMapperInterface
{
    /**
     * @param list<array<string, mixed>> $items
     */
    public function map(array $items): AttributeDefinitionCollection
    {
        $collection = new AttributeDefinitionCollection();
        if ($items === []) {
            return $collection;
        }
        foreach ($items as $item) {
            /** @var array<string, mixed> $item */
            if (!$this->canProcess($item)) {
                continue;
            }
            $collection->add($this->mapItem(self::ensureStringKeys($item)));
        }
        return $collection;
    }

    public function canProcess(array $item): bool
    {
        return isset($item['id'], $item['name']);
    }

    public function mapItem(mixed $item): AttributeDefinitionDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        return new AttributeDefinitionDto(
            id: ArrayHelper::asString($item['id'] ?? ''),
            type: ArrayHelper::asString($item['type'] ?? ''),
            expectedValue: ArrayHelper::asString($item['expectedValue'] ?? ''),
            name: ArrayHelper::asString($item['name'] ?? ''),
            lang: isset($item['lang']) ? ArrayHelper::asString($item['lang']) : null,
            description: isset($item['description']) ? ArrayHelper::asString($item['description']) : null,
            dictionary: isset($item['dictionary']) && is_array($item['dictionary'])
                ? self::ensureStringKeys(
                    /** @var array<string, mixed> */ $item['dictionary']
                )
                : null,
        );
    }

    /**
     * Type-narrowing for API response arrays (JSON objects always have string keys).
     *
     * @param array<string, mixed> $arr Attribute item or dictionary object from API
     * @return array<string, mixed>
     */
    private static function ensureStringKeys(array $arr): array
    {
        return $arr;
    }
}
