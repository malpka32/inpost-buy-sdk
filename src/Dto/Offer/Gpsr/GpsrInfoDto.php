<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Gpsr;

use malpka32\InPostBuySdk\Collection\GpsrManualCollection;

/**
 * GPSR information – EU product safety regulations (OpenAPI: GpsrInfo).
 *
 * General Product Safety Regulation – required for some categories.
 * doesNotRequireGpsrInfo in the category exempts from this requirement.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class GpsrInfoDto
{
    public function __construct(
        /** Product manuals. */
        public ?GpsrManualCollection $manuals = null,
        /** Manufacturer data (required for GPSR). */
        public ?GpsrManufacturerDto $manufacturer = null,
        /** Product introduced before 13.12.2024 – does not require full GPSR information. */
        public ?bool $doesNotRequireGpsrInfo = null,
        /** Safety information. */
        public ?string $safetyInformation = null,
        /** Batch number. */
        public ?string $batchNumber = null,
        /** Whether the product has CE marking. */
        public ?bool $ceMarking = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $result = [];
        if ($this->manuals !== null && !$this->manuals->isEmpty()) {
            $result['manuals'] = array_map(
                static fn (GpsrManualDto $m) => $m->toArray(),
                $this->manuals->toArray(),
            );
        }
        if ($this->manufacturer !== null) {
            $result['manufacturer'] = $this->manufacturer->toArray();
        }
        if ($this->doesNotRequireGpsrInfo !== null) {
            $result['doesNotRequireGpsrInfo'] = $this->doesNotRequireGpsrInfo;
        }
        if (!empty($this->safetyInformation)) {
            $result['safetyInformation'] = $this->safetyInformation;
        }
        if (!empty($this->batchNumber)) {
            $result['batchNumber'] = $this->batchNumber;
        }
        if ($this->ceMarking !== null) {
            $result['ceMarking'] = $this->ceMarking;
        }
        return $result;
    }
}
