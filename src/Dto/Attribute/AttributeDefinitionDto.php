<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Attribute;

/**
 * Definicja atrybutu kategorii (OpenAPI: AttributeDefinition).
 *
 * Describes required/optional attribute for a product in a given category.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#operation/getCategoriesAttributesByCategoryIdV1
 */
final class AttributeDefinitionDto
{
    /**
     * @param array<string, mixed>|null $dictionary Allowed values dictionary (for type=DICTIONARY)
     */
    public function __construct(
        public string $id,
        public string $type,
        public string $expectedValue,
        public string $name,
        public ?string $lang = null,
        public ?string $description = null,
        public ?array $dictionary = null,
    ) {
    }
}
