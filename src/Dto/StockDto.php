<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * Offer stock (OpenAPI: Stock).
 * quantity – item count, unit – unit (e.g. UNIT, PAIR, SET).
 */
final class StockDto
{
    public function __construct(
        public int $quantity,
        public string $unit,
    ) {
    }

    /** @return array{quantity: int, unit: string} */
    public function toArray(): array
    {
        return [
            'quantity' => $this->quantity,
            'unit' => $this->unit,
        ];
    }
}
