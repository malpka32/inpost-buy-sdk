<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * API error response (OpenAPI: ErrorResponse).
 *
 * @param list<ErrorDetailDto> $details
 */
final class ErrorResponseDto
{
    public function __construct(
        public string $errorCode,
        public ?string $errorMessage = null,
        public array $details = [],
    ) {
    }
}
