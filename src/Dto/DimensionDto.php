<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * Product packaging dimensions (OpenAPI: Dimension).
 * Width, height and length in mm, weight in g.
 */
final class DimensionDto
{
    public function __construct(
        public int $width,
        public int $height,
        public int $length,
        public int $weight,
    ) {
    }

    /** @return array{width: int, height: int, length: int, weight: int} */
    public function toArray(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
            'length' => $this->length,
            'weight' => $this->weight,
        ];
    }
}
