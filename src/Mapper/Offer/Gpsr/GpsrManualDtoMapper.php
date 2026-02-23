<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Gpsr;

use malpka32\InPostBuySdk\Dto\Offer\Gpsr\GpsrManualDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements ItemMapperInterface<GpsrManualDto>
 */
final class GpsrManualDtoMapper implements ItemMapperInterface
{
    public function canProcess(array $item): bool
    {
        return isset($item['title'], $item['url']);
    }

    public function mapItem(mixed $item): GpsrManualDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        return new GpsrManualDto(
            ArrayHelper::asString($item['title'] ?? ''),
            ArrayHelper::asString($item['url'] ?? ''),
        );
    }
}
