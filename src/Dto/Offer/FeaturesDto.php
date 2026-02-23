<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer;

/**
 * Cechy oferty (OpenAPI: Features).
 *
 * Dodatkowe flagi oferty, m.in. czy podlega zwrotowi.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class FeaturesDto
{
    public function __construct(
        /** Czy oferta podlega zwrotowi. Domyślnie true. */
        public bool $refundable = true,
    ) {
    }

    /** @return array{refundable: bool} */
    public function toArray(): array
    {
        return ['refundable' => $this->refundable];
    }
}
