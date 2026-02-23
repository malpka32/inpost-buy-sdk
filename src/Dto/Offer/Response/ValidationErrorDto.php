<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Response;

use malpka32\InPostBuySdk\Helper\ArrayHelper;

/**
 * Single validation error from offer metadata (API: OfferDetails.metadata.validationErrors).
 */
final class ValidationErrorDto
{
    public function __construct(
        public string $validationCode,
        public string $validationMessage,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayHelper::asString($data['validationCode'] ?? ''),
            ArrayHelper::asString($data['validationMessage'] ?? ''),
        );
    }
}
