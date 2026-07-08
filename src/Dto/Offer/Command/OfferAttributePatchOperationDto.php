<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Command;

use malpka32\InPostBuySdk\Dto\Offer\OfferAttributePatchOperationType;

/**
 * Single operation for Patch Offer attributes.
 *
 * UPSERT requires values (and optionally lang); REMOVE only needs the attribute id.
 * Payload item: { type, id, values?, lang? }.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/patchOffersAttributesV1
 */
final class OfferAttributePatchOperationDto
{
    /**
     * @param list<string>|null $values Values for UPSERT (ignored for REMOVE)
     */
    public function __construct(
        /** Rodzaj operacji (upsert/remove). */
        public OfferAttributePatchOperationType $type,
        /** UUID atrybutu. */
        public string $id,
        /** Wartości atrybutu (dla UPSERT). */
        public ?array $values = null,
        /** Kod języka (np. pl_PL) dla wartości lang-specific. */
        public ?string $lang = null,
    ) {
    }

    /**
     * Convenience factory for an upsert operation.
     *
     * @param list<string> $values
     */
    public static function upsert(string $id, array $values, ?string $lang = null): self
    {
        return new self(OfferAttributePatchOperationType::UPSERT, $id, $values, $lang);
    }

    /**
     * Convenience factory for a remove operation.
     */
    public static function remove(string $id): self
    {
        return new self(OfferAttributePatchOperationType::REMOVE, $id);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $payload = [
            'type' => $this->type->value,
            'id' => $this->id,
        ];
        if ($this->values !== null) {
            $payload['values'] = $this->values;
        }
        if ($this->lang !== null && $this->lang !== '') {
            $payload['lang'] = $this->lang;
        }

        return $payload;
    }
}
