<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Attribute\AttributeDefinitionDto;
use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<AttributeDefinitionDto>
 */
final class AttributeDefinitionCollection extends AbstractCollection
{
    public function getType(): string
    {
        return \malpka32\InPostBuySdk\Dto\Attribute\AttributeDefinitionDto::class;
    }
}
