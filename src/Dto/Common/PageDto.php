<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Common;

use malpka32\InPostBuySdk\Helper\ArrayHelper;

/**
 * Pagination info from API (limit, offset, total).
 */
final class PageDto
{
    public function __construct(
        public int $limit,
        public int $offset,
        public int $total,
    ) {
    }

    /**
     * @param array<string, mixed> $data Raw API page object
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ArrayHelper::asInt($data['limit'] ?? 0),
            ArrayHelper::asInt($data['offset'] ?? 0),
            ArrayHelper::asInt($data['total'] ?? 0),
        );
    }
}
