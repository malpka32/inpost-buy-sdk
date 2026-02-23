<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Response;

/**
 * Offer metadata from API (validation errors, rejection reasons).
 * Present in Offer Details response (GET/PATCH) – { metadata, offer }.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers
 */
final class OfferMetadataDto
{
    /**
     * @param list<ValidationErrorDto>   $validationErrors
     * @param list<RejectionReasonDto> $rejectionReasons
     */
    public function __construct(
        public readonly array $validationErrors,
        public readonly array $rejectionReasons,
    ) {
    }

    /**
     * @param array<string, mixed> $data Raw API metadata { validationErrors?, rejectionReasons? }
     */
    public static function fromArray(array $data): self
    {
        return new self(
            self::mapValidationErrors($data['validationErrors'] ?? null),
            self::mapRejectionReasons($data['rejectionReasons'] ?? null),
        );
    }

    /**
     * @param mixed $raw
     * @return list<ValidationErrorDto>
     */
    private static function mapValidationErrors(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }

        $filtered = array_values(array_filter($raw, 'is_array'));
        /** @var list<array<string, mixed>> $filtered */
        return array_map(
            static fn (array $item): ValidationErrorDto => ValidationErrorDto::fromArray($item),
            $filtered,
        );
    }

    /**
     * @param mixed $raw
     * @return list<RejectionReasonDto>
     */
    private static function mapRejectionReasons(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }

        $filtered = array_values(array_filter($raw, 'is_array'));
        /** @var list<array<string, mixed>> $filtered */
        return array_map(
            static fn (array $item): RejectionReasonDto => RejectionReasonDto::fromArray($item),
            $filtered,
        );
    }
}
