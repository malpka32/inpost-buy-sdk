<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Core;

use malpka32\InPostBuySdk\Dto\Offer\Product\DimensionDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * Maps dimensions array (OpenAPI: Dimension) to DimensionDto.
 */
/**
 * @implements ItemMapperInterface<DimensionDto>
 */
final class DimensionMapper implements ItemMapperInterface
{
    /**
     * @param array<string, mixed> $item
     */
    public function canProcess(array $item): bool
    {
        return isset($item['width'], $item['height'], $item['length'], $item['weight']);
    }

    public function mapItem(mixed $item): DimensionDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        return new DimensionDto(
            ArrayHelper::asInt($item['width']),
            ArrayHelper::asInt($item['height']),
            ArrayHelper::asInt($item['length']),
            ArrayHelper::asInt($item['weight']),
        );
    }

    /**
     * @param array<string, mixed>|null $data
     */
    public function map(?array $data): ?DimensionDto
    {
        if ($data === null || !$this->canProcess($data)) {
            return null;
        }

        return $this->mapItem($data);
    }
}
