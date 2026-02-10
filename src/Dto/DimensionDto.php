<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * Wymiary opakowania produktu (OpenAPI: Dimension).
 * Szerokość, wysokość i długość w mm, waga w g.
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
}
