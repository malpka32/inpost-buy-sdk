<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Gpsr;

use malpka32\InPostBuySdk\Dto\Offer\Gpsr\GpsrManufacturerDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<GpsrManufacturerDto>
 */
final class GpsrManufacturerSingleMapper implements SingleItemMapperInterface
{
    public function map(mixed $data): ?GpsrManufacturerDto
    {
        if (!is_array($data) || !isset($data['name'], $data['email'])) {
            return null;
        }
        /** @var array<string, mixed> $data */
        $addressRaw = $data['address'] ?? null;
        $address = is_array($addressRaw) ? $addressRaw : null;
        return new GpsrManufacturerDto(
            name: ArrayHelper::asString($data['name']),
            email: ArrayHelper::asString($data['email']),
            phone: isset($data['phone']) ? ArrayHelper::asString($data['phone']) : null,
            unstructuredAddress: isset($data['unstructuredAddress']) ? ArrayHelper::asString($data['unstructuredAddress']) : null,
            address: $address,
            responsiblePerson: isset($data['responsiblePerson']) ? ArrayHelper::asString($data['responsiblePerson']) : null,
        );
    }
}
