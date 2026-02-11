<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper;

use malpka32\InPostBuySdk\Dto\DimensionDto;

/**
 * Maps dimensions array (OpenAPI: Dimension) to DimensionDto.
 */
final class DimensionMapper
{
    /**
     * @param array<string, mixed> $data
     */
    public function map(?array $data): ?DimensionDto
    {
        if ($data === null || !isset($data['width'], $data['height'], $data['length'], $data['weight'])) {
            return null;
        }
        return new DimensionDto(
            (int) $data['width'],
            (int) $data['height'],
            (int) $data['length'],
            (int) $data['weight'],
        );
    }
}
