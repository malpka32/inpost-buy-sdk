<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper;

use malpka32\InPostBuySdk\Collection\AttributeValueCollection;
use malpka32\InPostBuySdk\Dto\AttributeValueDto;

/**
 * Maps attributes array (OpenAPI: AttributeValue) to AttributeValueCollection.
 */
final class AttributeValueMapper
{
    /**
     * @param list<array<string, mixed>>|null $data
     */
    public function map(?array $data): AttributeValueCollection
    {
        $collection = new AttributeValueCollection();
        if ($data === null || $data === []) {
            return $collection;
        }
        foreach ($data as $item) {
            if (empty($item['id']) || !isset($item['values'])) {
                continue;
            }
            $rawValues = $item['values'];
            $values = is_array($rawValues)
                ? array_values(array_map(fn (mixed $v): string => ArrayHelper::asString($v), $rawValues))
                : [];
            $collection->add(new AttributeValueDto(
                id: ArrayHelper::asString($item['id']),
                values: $values,
                lang: isset($item['lang']) ? ArrayHelper::asString($item['lang']) : null,
            ));
        }
        return $collection;
    }
}
