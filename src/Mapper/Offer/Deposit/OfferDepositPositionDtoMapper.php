<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Deposit;

use malpka32\InPostBuySdk\Dto\Offer\Deposit\DepositPositionDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements ItemMapperInterface<DepositPositionDto>
 */
final class OfferDepositPositionDtoMapper implements ItemMapperInterface
{
    public function __construct(
        private readonly DepositTypeMapper $depositTypeMapper = new DepositTypeMapper(),
    ) {
    }

    public function canProcess(array $item): bool
    {
        return isset($item['depositType']) && is_array($item['depositType']);
    }

    public function mapItem(mixed $item): DepositPositionDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        $depositType = $this->depositTypeMapper->mapItem($item['depositType'] ?? null);

        return new DepositPositionDto(
            quantity: ArrayHelper::asInt(ArrayHelper::get($item, 'quantity') ?? 0),
            depositType: $depositType,
        );
    }
}
