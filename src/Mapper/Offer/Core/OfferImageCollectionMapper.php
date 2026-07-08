<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Core;

use malpka32\InPostBuySdk\Collection\OfferImageCollection;
use malpka32\InPostBuySdk\Dto\Offer\Image\OfferImageDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;

/**
 * Maps raw offer images list into a typed OfferImageCollection.
 */
final class OfferImageCollectionMapper
{
    public function map(mixed $imagesRaw): ?OfferImageCollection
    {
        if (!is_array($imagesRaw)) {
            return null;
        }

        $collection = new OfferImageCollection();
        foreach ($imagesRaw as $item) {
            if (!is_array($item)) {
                continue;
            }
            /** @var array<string, mixed> $item */
            $fileName = ArrayHelper::get($item, 'fileName');
            if ($fileName === null) {
                continue;
            }
            $fileUrl = ArrayHelper::get($item, 'fileUrl');
            $priority = ArrayHelper::get($item, 'priority');
            $collection->add(new OfferImageDto(
                fileName: ArrayHelper::asString($fileName),
                fileUrl: $fileUrl !== null ? ArrayHelper::asString($fileUrl) : null,
                priority: $priority !== null ? ArrayHelper::asInt($priority) : null,
            ));
        }

        return $collection->isEmpty() ? null : $collection;
    }
}
