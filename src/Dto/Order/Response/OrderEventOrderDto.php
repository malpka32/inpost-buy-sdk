<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Response;

use malpka32\InPostBuySdk\Helper\ArrayHelper;

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
        $idRaw = $data['id'] ?? null;
        $id = $idRaw === null ? null : ArrayHelper::asString($idRaw);
        if ($id === '') {
            $id = null;
        }

        return new self(
            id: $id,
        );
    }
}
