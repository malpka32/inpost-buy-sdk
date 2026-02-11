<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Exception;

use malpka32\InPostBuySdk\Dto\ErrorDetailDto;
use malpka32\InPostBuySdk\Dto\ErrorResponseDto;
use malpka32\InPostBuySdk\Mapper\ArrayHelper;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Creates specific exception type from HTTP response.
 */
final class ApiExceptionFactory
{
    public static function fromResponse(
        ResponseInterface $response,
        string $contextMessage = 'API error'
    ): ApiException {
        $statusCode = $response->getStatusCode();
        $body = $response->getContent(false);
        $errorResponse = self::parseErrorBody($body);
        $headers = self::normalizeHeaders($response->getHeaders(false));

        $message = $errorResponse !== null ? ($errorResponse->errorMessage ?? $contextMessage) : $contextMessage;

        $exception = match ($statusCode) {
            400 => new BadRequestException($message, $statusCode, $body, $errorResponse, $headers),
            401 => new UnauthorizedException($message, $statusCode, $body, $errorResponse, $headers),
            403 => new ForbiddenException($message, $statusCode, $body, $errorResponse, $headers),
            404 => new NotFoundException($message, $statusCode, $body, $errorResponse, $headers),
            415 => new UnsupportedMediaTypeException($message, $statusCode, $body, $errorResponse, $headers),
            422 => new UnprocessableEntityException($message, $statusCode, $body, $errorResponse, $headers),
            429 => new TooManyRequestsException($message, $statusCode, $body, $errorResponse, $headers),
            default => $statusCode >= 500
                ? new ServerException($message, $statusCode, $body, $errorResponse, $headers)
                : new ApiException($message, $statusCode, $body, $errorResponse, $headers),
        };

        return $exception;
    }

    private static function parseErrorBody(string $body): ?ErrorResponseDto
    {
        if ($body === '') {
            return null;
        }
        $data = json_decode($body, true);
        if (!is_array($data) || empty($data['errorCode'])) {
            return null;
        }
        $details = [];
        $detailsRaw = $data['details'] ?? [];
        if (is_array($detailsRaw)) {
            foreach ($detailsRaw as $item) {
                if (is_array($item)) {
                    $field = $item['field'] ?? null;
                    $detail = $item['detail'] ?? null;
                    $details[] = new ErrorDetailDto(
                        $field === null ? null : ArrayHelper::asString($field),
                        $detail === null ? null : ArrayHelper::asString($detail)
                    );
                }
            }
        }
        $errorMessage = $data['errorMessage'] ?? null;
        return new ErrorResponseDto(
            ArrayHelper::asString($data['errorCode']),
            $errorMessage === null ? null : ArrayHelper::asString($errorMessage),
            $details
        );
    }

    /**
     * @param array<string, string[]> $rawHeaders
     * @return array<string, string>
     */
    private static function normalizeHeaders(array $rawHeaders): array
    {
        $normalized = [];
        foreach ($rawHeaders as $key => $values) {
            $lower = strtolower($key);
            $normalized[$lower] = $values[0] ?? '';
        }
        return $normalized;
    }
}
