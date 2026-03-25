<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Core;

use malpka32\InPostBuySdk\Dto\Order\Core\OrderPaymentDetailsDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<OrderPaymentDetailsDto>
 */
final class OrderPaymentDetailsDtoMapper implements SingleItemMapperInterface
{
    public function __construct(
        private readonly OrderPaymentCollectionMapper $paymentsMapper = new OrderPaymentCollectionMapper(),
    ) {
    }

    public function map(mixed $data): ?OrderPaymentDetailsDto
    {
        if (!is_array($data)) {
            return null;
        }
        $selectedPaymentType = ArrayHelper::get($data, 'selectedPaymentType');
        $paymentsRaw = ArrayHelper::get($data, 'payments');
        $payments = is_array($paymentsRaw) ? $this->paymentsMapper->map($paymentsRaw) : null;

        return new OrderPaymentDetailsDto(
            selectedPaymentType: $selectedPaymentType === null ? null : ArrayHelper::asString($selectedPaymentType),
            payments: $payments,
        );
    }
}
