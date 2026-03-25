<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Command;

use malpka32\InPostBuySdk\Helper\ArrayHelper;

/**
 * Details about processed order command (e.g. accept/refuse/refund).
 */
final class OrderCommandStatusDto
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
