<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * Category attribute value (OpenAPI: AttributeValue).
 * id – attribute UUID, values – value list (e.g. strings).
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
