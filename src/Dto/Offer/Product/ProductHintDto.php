<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Product;

/**
 * Product hint item from API (product info, GPSR, category mapping).
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/getOffersHintV1
 */
final class ProductHintDto
{
    /**
     * @param array<string, mixed> $raw Raw hint item from API
     */
    public function __construct(
        private readonly array $raw,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->raw;
    }
}
