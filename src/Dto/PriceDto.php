<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * Offer price (OpenAPI: PriceTag).
 * amount, currency – grossPrice part; taxRateInfo – VAT rate info (e.g. "23%").
 */
final class PriceDto
{
    public function __construct(
        public float $amount,
        public string $currency,
        public string $taxRateInfo,
    ) {
    }

    /** @return array{grossPrice: array{amount: float, currency: string}, taxRateInfo: string} */
    public function toArray(): array
    {
        return [
            'grossPrice' => [
                'amount' => round($this->amount, 2),
                'currency' => $this->currency,
            ],
            'taxRateInfo' => $this->taxRateInfo,
        ];
    }
}
