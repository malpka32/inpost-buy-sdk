<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Gpsr;

use malpka32\InPostBuySdk\Collection\GpsrManualCollection;
use malpka32\InPostBuySdk\Dto\Offer\Gpsr\GpsrInfoDto;
use malpka32\InPostBuySdk\Dto\Offer\Gpsr\GpsrManufacturerDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<GpsrInfoDto>
 */
final class OfferGpsrSingleMapper implements SingleItemMapperInterface
{
    public function __construct(
        private readonly GpsrManualCollectionMapper $manualsMapper = new GpsrManualCollectionMapper(),
        private readonly GpsrManufacturerSingleMapper $manufacturerMapper = new GpsrManufacturerSingleMapper(),
    ) {
    }

    public function map(mixed $data): ?GpsrInfoDto
    {
        if (!is_array($data)) {
            return null;
        }
        /** @var array<string, mixed> $data */
        $manualsRaw = $data['manuals'] ?? [];
        $manuals = is_array($manualsRaw)
            ? $this->manualsMapper->map($this->toManualsList($manualsRaw))
            : new GpsrManualCollection();
        $manufacturer = $this->manufacturerMapper->map($data['manufacturer'] ?? null);

        $doesNotRequire = ArrayHelper::get($data, 'doesNotRequireGpsrInfo');
        $safety = ArrayHelper::get($data, 'safetyInformation');
        $batch = ArrayHelper::get($data, 'batchNumber');
        $ce = ArrayHelper::get($data, 'ceMarking');

        if (!$this->hasGpsrData($data, $manuals, $manufacturer, $doesNotRequire, $safety, $batch, $ce)) {
            return null;
        }

        return new GpsrInfoDto(
            manuals: $manuals->isEmpty() ? null : $manuals,
            manufacturer: $manufacturer,
            doesNotRequireGpsrInfo: $doesNotRequire === null ? null : (bool) $doesNotRequire,
            safetyInformation: $safety === null ? null : ArrayHelper::asString($safety),
            batchNumber: $batch === null ? null : ArrayHelper::asString($batch),
            ceMarking: $ce === null ? null : (bool) $ce,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function hasGpsrData(
        array $data,
        GpsrManualCollection $manuals,
        ?GpsrManufacturerDto $manufacturer,
        mixed $doesNotRequire,
        mixed $safety,
        mixed $batch,
        mixed $ce,
    ): bool {
        return !$manuals->isEmpty()
            || $manufacturer !== null
            || array_key_exists('doesNotRequireGpsrInfo', $data)
            || !empty($safety)
            || !empty($batch)
            || array_key_exists('ceMarking', $data);
    }

    /**
     * @param array<mixed> $raw
     * @return list<array<string, mixed>>
     */
    private function toManualsList(array $raw): array
    {
        $filtered = array_values(array_filter($raw, 'is_array'));
        /** @var list<array<string, mixed>> $filtered */
        return $filtered;
    }
}
