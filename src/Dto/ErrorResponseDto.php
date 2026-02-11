<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto;

/**
 * API error response (OpenAPI: ErrorResponse).
 */
final class ErrorResponseDto
{
    /**
     * @param list<ErrorDetailDto> $details
     */
    public function __construct(
        public string $errorCode,
        public ?string $errorMessage = null,
        /** @var list<ErrorDetailDto> */
        public array $details = [],
    ) {
    }
}
