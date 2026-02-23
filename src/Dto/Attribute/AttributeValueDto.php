<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Attribute;

/**
 * Category attribute value (OpenAPI: AttributeValue).
 * id – attribute UUID, values – value list (e.g. strings).
 * Used in product attributes (offer) and in category context.
 * Pure data object; mapping from API is done by AttributeValueDtoMapper.
 */
final class AttributeValueDto
{
    /** @param list<string> $values */
    public function __construct(
        public string $id,
        public array $values,
        public ?string $lang = null,
    ) {
    }
}
