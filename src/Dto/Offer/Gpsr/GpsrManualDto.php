<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Gpsr;

/**
 * Product manual (OpenAPI: Manual).
 *
 * Used in GPSR – product safety regulations information (EU).
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class GpsrManualDto
{
    public function __construct(
        /** Manual title (5–500 characters). */
        public string $title,
        /** URL to PDF file (9–2048 characters). */
        public string $url,
    ) {
    }

    /** @return array{title: string, url: string} */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
        ];
    }
}
