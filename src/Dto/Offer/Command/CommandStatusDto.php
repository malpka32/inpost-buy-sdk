<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Command;

use malpka32\InPostBuySdk\Helper\ArrayHelper;

/**
 * Details about a processed command (close/reopen/attachment/create, command status).
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers
 */
final class CommandStatusDto
{
    public function __construct(
        public string $commandId,
        public string $status,
    ) {
    }

    /**
     * @param array<string, mixed> $data Raw API response { commandId, status }
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayHelper::asString($data['commandId'] ?? ''),
            ArrayHelper::asString($data['status'] ?? ''),
        );
    }
}
