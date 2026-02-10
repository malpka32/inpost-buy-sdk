<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

final class CategoryDto
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $parentId = null,
    ) {
    }
}
