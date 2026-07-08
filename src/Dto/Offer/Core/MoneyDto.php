<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Core;

use malpka32\InPostBuySdk\Helper\ArrayHelper;

/**
 * Money – representation of amount and currency (OpenAPI: Money).
 *
 * Used e.g. in batch price update, where only amount + currency is required
 * (unlike PriceTag, which also carries taxRateInfo and deposits).
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers
 */
final class MoneyDto
{
    public function __construct(
        /** Kwota. */
        public float $amount,
        /** Waluta (np. PLN). */
        public string $currency,
    ) {
    }

    /**
     * @param array<string, mixed> $data Raw API money object { amount, currency }
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayHelper::asFloat($data['amount'] ?? 0.0),
            ArrayHelper::asString(ArrayHelper::get($data, 'currency') ?? 'PLN'),
        );
    }

    /** @return array{amount: float, currency: string} */
    public function toArray(): array
    {
        return [
            'amount' => round($this->amount, 2),
            'currency' => $this->currency,
        ];
    }
}
