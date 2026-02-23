<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\PostSale;

use malpka32\InPostBuySdk\Dto\Offer\FeaturesDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<FeaturesDto>
 */
final class OfferFeaturesSingleMapper implements SingleItemMapperInterface
{
    public function map(mixed $data): ?FeaturesDto
    {
        if (!is_array($data)) {
            return null;
        }
        if (!array_key_exists('refundable', $data)) {
            return null;
        }

        return new FeaturesDto(
            refundable: ArrayHelper::asBoolOrNull($data['refundable'] ?? null) ?? true,
        );
    }
}
