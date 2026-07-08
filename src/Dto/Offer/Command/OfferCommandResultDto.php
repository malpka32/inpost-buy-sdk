<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Command;

use malpka32\InPostBuySdk\Helper\ArrayHelper;

/**
 * Result of an offer command that references a specific offer
 * (batch price/stock update, attributes patch).
 *
 * Raw API shape: { commandId, offerId, status }.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers
 */
final class OfferCommandResultDto
{
    public function __construct(
        public string $commandId,
        public string $offerId,
        public string $status,
    ) {
    }

    /**
     * @param array<string, mixed> $data Raw API response { commandId, offerId, status }
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayHelper::asString($data['commandId'] ?? ''),
            ArrayHelper::asString($data['offerId'] ?? $data['id'] ?? ''),
            ArrayHelper::asString($data['status'] ?? ''),
        );
    }
}
