<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Response;

/**
 * Nested order reference in order event payload.
 */
final class OrderEventOrderDto
{
    public function __construct(
        public ?string $id = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;

        return new self(
            id: $id === null ? null : (string) $id,
        );
    }
}
