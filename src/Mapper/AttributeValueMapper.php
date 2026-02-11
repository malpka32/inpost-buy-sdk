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
            if (!is_array($item) || empty($item['id']) || !isset($item['values'])) {
                continue;
            }
            $collection->add(new AttributeValueDto(
                id: (string) $item['id'],
                values: is_array($item['values']) ? array_map('strval', $item['values']) : [],
                lang: isset($item['lang']) ? (string) $item['lang'] : null,
            ));
        }
        return $collection;
    }
}
