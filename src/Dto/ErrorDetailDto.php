<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * Error detail from API response (OpenAPI: ErrorDetail).
 */
final class ErrorDetailDto
{
    public function __construct(
        public ?string $field = null,
        public ?string $detail = null,
    ) {
    }
}
