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
        /** @var array<string, mixed> $data */
        $selectedPaymentType = ArrayHelper::get($data, 'selectedPaymentType');
        $paymentsRaw = ArrayHelper::get($data, 'payments');
        if (is_array($paymentsRaw) && array_is_list($paymentsRaw)) {
            /** @var list<mixed> $paymentsRaw */
            $payments = $this->paymentsMapper->map($paymentsRaw);
        } else {
            $payments = null;
        }

        return new OrderPaymentDetailsDto(
            selectedPaymentType: $selectedPaymentType === null ? null : ArrayHelper::asString($selectedPaymentType),
            payments: $payments,
        );
    }
}
