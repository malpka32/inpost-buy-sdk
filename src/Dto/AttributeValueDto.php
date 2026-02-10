<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * Wartość atrybutu kategorii (OpenAPI: AttributeValue).
 * id – UUID atrybutu, values – lista wartości (np. stringi).
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
