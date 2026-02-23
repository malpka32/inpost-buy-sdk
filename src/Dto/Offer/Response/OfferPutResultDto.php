<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Response;

use malpka32\InPostBuySdk\Helper\ArrayHelper;

/**
 * Result of creating or updating a single offer (PUT/POST/PATCH response).
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers
 */
final class OfferPutResultDto
{
    public function __construct(
        public string $commandId,
        public string $offerId,
        public string $externalId,
    ) {
    }

    /**
     * @param array<string, mixed> $data Raw API response { commandId, offerId, externalId }
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayHelper::asString($data['commandId'] ?? ''),
            ArrayHelper::asString($data['offerId'] ?? $data['id'] ?? ''),
            ArrayHelper::asString($data['externalId'] ?? ''),
        );
    }
}
