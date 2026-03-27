<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer;

/**
 * Offer features (OpenAPI: Features).
 *
 * Additional offer flags, e.g. whether refundable.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class FeaturesDto
{
    public function __construct(
        /** Whether the offer is refundable. Default: true. */
        public bool $refundable = true,
    ) {
    }

    /** @return array{refundable: bool} */
    public function toArray(): array
    {
        return ['refundable' => $this->refundable];
    }
}
