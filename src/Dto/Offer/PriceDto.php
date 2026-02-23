<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer;

use malpka32\InPostBuySdk\Collection\DepositPositionCollection;
use malpka32\InPostBuySdk\Dto\Offer\Deposit\DepositPositionDto;

/**
 * Cena oferty (OpenAPI: PriceTag).
 *
 * Gross price, optional deposits, and VAT rate info.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class PriceDto
{
    /**
     * @param DepositPositionCollection|list<DepositPositionDto>|null $deposits Deposits for packaging (e.g. bottles, cans)
     */
    public function __construct(
        /** Kwota ceny brutto. */
        public float $amount,
        /** Currency (e.g. PLN). */
        public string $currency,
        /** VAT rate info (e.g. "23%"). */
        public string $taxRateInfo,
        DepositPositionCollection|array|null $deposits = null,
    ) {
        if ($deposits instanceof DepositPositionCollection) {
            $this->deposits = $deposits;
            return;
        }
        $this->deposits = is_array($deposits) ? DepositPositionCollection::fromArray($deposits) : null;
    }

    /** Deposit positions – optional, for products with deposit. */
    public ?DepositPositionCollection $deposits;

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $result = [
            'grossPrice' => [
                'amount' => round($this->amount, 2),
                'currency' => $this->currency,
            ],
            'taxRateInfo' => $this->taxRateInfo,
        ];
        if ($this->deposits !== null && !$this->deposits->isEmpty()) {
            $result['deposits'] = array_map(
                static fn (DepositPositionDto $d) => $d->toArray(),
                $this->deposits->toArray(),
            );
        }
        return $result;
    }
}
