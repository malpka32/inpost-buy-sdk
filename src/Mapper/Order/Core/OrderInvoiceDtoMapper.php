<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Core;

use malpka32\InPostBuySdk\Dto\Order\Core\OrderAddressDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderInvoiceDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<OrderInvoiceDto>
 */
final class OrderInvoiceDtoMapper implements SingleItemMapperInterface
{
    public function __construct(
        private readonly SingleItemMapperInterface $addressMapper = new OrderAddressDtoMapper(),
    ) {
    }

    public function map(mixed $data): ?OrderInvoiceDto
    {
        if (!is_array($data)) {
            return null;
        }

        $email = ArrayHelper::get($data, 'email');
        $legalForm = ArrayHelper::get($data, 'legalForm');
        $companyName = ArrayHelper::get($data, 'companyName');
        $firstName = ArrayHelper::get($data, 'firstName');
        $lastName = ArrayHelper::get($data, 'lastName');
        $taxIdPrefix = ArrayHelper::get($data, 'taxIdPrefix');
        $taxId = ArrayHelper::get($data, 'taxId');
        $address = $this->addressMapper->map(ArrayHelper::get($data, 'address'));

        return new OrderInvoiceDto(
            email: $email === null ? null : ArrayHelper::asString($email),
            legalForm: $legalForm === null ? null : ArrayHelper::asString($legalForm),
            companyName: $companyName === null ? null : ArrayHelper::asString($companyName),
            firstName: $firstName === null ? null : ArrayHelper::asString($firstName),
            lastName: $lastName === null ? null : ArrayHelper::asString($lastName),
            taxIdPrefix: $taxIdPrefix === null ? null : ArrayHelper::asString($taxIdPrefix),
            taxId: $taxId === null ? null : ArrayHelper::asString($taxId),
            address: $address instanceof OrderAddressDto ? $address : null,
        );
    }
}
