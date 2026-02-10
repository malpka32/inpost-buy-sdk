<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Exception;

use malpka32\InPostBuySdk\Dto\ErrorResponseDto;

/**
 * Base exception for InPost Buy API errors.
 */
class ApiException extends \RuntimeException
{
    private ?string $responseBody = null;
    private ?ErrorResponseDto $errorResponse = null;

    /** @var array<string, string> */
    private array $responseHeaders = [];

    /**
     * @param array<string, string> $responseHeaders
     */
    public function __construct(
        string $message,
        private readonly ?int $statusCode = null,
        ?string $responseBody = null,
        ?ErrorResponseDto $errorResponse = null,
        array $responseHeaders = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode ?? 0, $previous);
        $this->responseBody = $responseBody;
        $this->errorResponse = $errorResponse;
        $this->responseHeaders = $responseHeaders;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }

    public function getErrorResponse(): ?ErrorResponseDto
    {
        return $this->errorResponse;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorResponse?->errorCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorResponse?->errorMessage;
    }

    /**
     * Whether the error is retry-able (5xx, 429).
     */
    public function isRetryable(): bool
    {
        if ($this->statusCode === null) {
            return false;
        }
        return $this->statusCode === 429 || $this->statusCode >= 500;
    }

    /**
     * Seconds until request retry (from Retry-After header).
     * Returns null when header is missing.
     */
    public function getRetryAfterSeconds(): ?int
    {
        $value = $this->responseHeaders['retry-after'] ?? null;
        if ($value === null) {
            return null;
        }
        if (ctype_digit((string) $value)) {
            return (int) $value;
        }
        $ts = strtotime((string) $value);
        return $ts !== false ? max(0, (int) ($ts - time())) : null;
    }

    /**
     * @return array<string, string>
     */
    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }
}
