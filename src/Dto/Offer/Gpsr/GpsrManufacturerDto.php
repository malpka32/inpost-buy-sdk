<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Gpsr;

/**
 * Manufacturer data for GPSR (OpenAPI: Manufacturer).
 *
 * Manufacturer contact info – required for some product categories.
 * Required fields: name, email.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class GpsrManufacturerDto
{
    /**
     * @param array<string, mixed>|null $address Structured address (street, city, postCode, countryCode, building, flat?, state?)
     */
    public function __construct(
        /** Nazwa producenta. */
        public string $name,
        /** Email kontaktowy. */
        public string $email,
        /** Phone number with country code (e.g. +48345678901). */
        public ?string $phone = null,
        /** Address formatted as single string. */
        public ?string $unstructuredAddress = null,
        /** Structured address (optional). */
        public ?array $address = null,
        /** Osoba odpowiedzialna. */
        public ?string $responsiblePerson = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $result = [
            'name' => $this->name,
            'email' => $this->email,
        ];
        if (!empty($this->phone)) {
            $result['phone'] = $this->phone;
        }
        if (!empty($this->unstructuredAddress)) {
            $result['unstructuredAddress'] = $this->unstructuredAddress;
        }
        if (!empty($this->address)) {
            $result['address'] = $this->address;
        }
        if (!empty($this->responsiblePerson)) {
            $result['responsiblePerson'] = $this->responsiblePerson;
        }
        return $result;
    }
}
