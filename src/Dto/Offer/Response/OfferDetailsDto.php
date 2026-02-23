<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Response;

use malpka32\InPostBuySdk\Dto\Offer\OfferDto;

/**
 * Full offer details from API – metadata + offer (GET offer, PATCH response).
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers
 */
final class OfferDetailsDto
{
    public function __construct(
        public readonly ?OfferMetadataDto $metadata,
        public readonly OfferDto $offer,
    ) {
    }
}
