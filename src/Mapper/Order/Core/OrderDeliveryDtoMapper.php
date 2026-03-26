<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Core;

use malpka32\InPostBuySdk\Collection\OrderDeliveryParcelCollection;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderAddressDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderDeliveryDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderDeliveryParcelDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderMoneyDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Helper\DateTimeHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<OrderDeliveryDto>
 */
final class OrderDeliveryDtoMapper implements SingleItemMapperInterface
{
    public function __construct(
        private readonly OrderAddressDtoMapper $addressMapper = new OrderAddressDtoMapper(),
        private readonly OrderMoneyDtoMapper $moneyMapper = new OrderMoneyDtoMapper(),
        /** @var ItemMapperInterface<OrderDeliveryParcelDto> */
        private readonly ItemMapperInterface $parcelMapper = new OrderDeliveryParcelDtoMapper(),
    ) {
    }

    public function map(mixed $data): ?OrderDeliveryDto
    {
        if (!is_array($data)) {
            return null;
        }
        /** @var array<string, mixed> $data */

        $deliveryType = ArrayHelper::get($data, 'deliveryType');
        $name = ArrayHelper::get($data, 'name');
        $deliveryPoint = ArrayHelper::get($data, 'deliveryPoint');
        $email = ArrayHelper::get($data, 'email');
        $phoneNumber = ArrayHelper::get($data, 'phoneNumber');

        $address = $this->addressMapper->map(ArrayHelper::get($data, 'address'));
        $price = $this->moneyMapper->map(ArrayHelper::get($data, 'price'));
        $expectedDeliveryDate = DateTimeHelper::parseOrNull(ArrayHelper::get($data, 'expectedDeliveryDate'));

        $parcelsRaw = ArrayHelper::get($data, 'parcels');
        $parcels = null;
        if (is_array($parcelsRaw) && array_is_list($parcelsRaw)) {
            $mappedParcels = new OrderDeliveryParcelCollection();
            foreach ($parcelsRaw as $parcelRaw) {
                if (!is_array($parcelRaw)) {
                    continue;
                }
                /** @var array<string, mixed> $parcelRaw */
                if (!$this->parcelMapper->canProcess($parcelRaw)) {
                    continue;
                }
                $mappedParcels->add($this->parcelMapper->mapItem($parcelRaw));
            }
            $parcels = $mappedParcels;
        }

        return new OrderDeliveryDto(
            deliveryType: $deliveryType === null ? null : ArrayHelper::asString($deliveryType),
            parcels: $parcels,
            name: $name === null ? null : ArrayHelper::asString($name),
            deliveryPoint: $deliveryPoint === null ? null : ArrayHelper::asString($deliveryPoint),
            address: $address instanceof OrderAddressDto ? $address : null,
            email: $email === null ? null : ArrayHelper::asString($email),
            phoneNumber: $phoneNumber === null ? null : ArrayHelper::asString($phoneNumber),
            price: $price instanceof OrderMoneyDto ? $price : null,
            expectedDeliveryDate: $expectedDeliveryDate,
        );
    }

}
