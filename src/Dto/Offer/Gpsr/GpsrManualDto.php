<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Gpsr;

/**
 * Instrukcja obsługi produktu (OpenAPI: Manual).
 *
 * Używana w GPSR – informacje o regulacjach bezpieczeństwa produktu (EU).
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class GpsrManualDto
{
    public function __construct(
        /** Tytuł instrukcji (5–500 znaków). */
        public string $title,
        /** URL do pliku PDF (9–2048 znaków). */
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
