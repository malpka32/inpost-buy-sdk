<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Response;

use malpka32\InPostBuySdk\Helper\ArrayHelper;

/**
 * Rejection reason from offer metadata (API: OfferDetails.metadata.rejectionReasons).
 */
final class RejectionReasonDto
{
    public function __construct(
        public string $reasonMessage,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayHelper::asString($data['reasonMessage'] ?? ''),
        );
    }
}
