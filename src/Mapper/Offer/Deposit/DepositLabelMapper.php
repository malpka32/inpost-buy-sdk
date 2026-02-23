<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Deposit;

use malpka32\InPostBuySdk\Collection\DepositLabelCollection;
use malpka32\InPostBuySdk\Dto\Offer\Deposit\DepositLabelDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\CollectionMapperInterface;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements CollectionMapperInterface<DepositLabelCollection>
 * @implements ItemMapperInterface<DepositLabelDto>
 */
final class DepositLabelMapper implements CollectionMapperInterface, ItemMapperInterface
{
    public function __construct(
        private readonly DepositTypeMapper $depositTypeMapper = new DepositTypeMapper(),
    ) {
    }

    /**
     * @param array<string, mixed> $data { data: DepositLabel[] }
     */
    public function map(array $data): DepositLabelCollection
    {
        $collection = new DepositLabelCollection();
        $items = $data['data'] ?? [];
        if (!is_array($items)) {
            return $collection;
        }
        foreach ($items as $item) {
            if (!is_array($item) || !$this->canProcess($item)) {
                continue;
            }
            $collection->add($this->mapItem($item));
        }
        return $collection;
    }

    public function canProcess(array $item): bool
    {
        return isset($item['name']) && isset($item['depositType']) && is_array($item['depositType']);
    }

    public function mapItem(mixed $item): DepositLabelDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        $depositType = $this->depositTypeMapper->mapItem($item['depositType'] ?? null);

        return new DepositLabelDto(
            name: ArrayHelper::asString($item['name'] ?? ''),
            depositType: $depositType,
        );
    }
}
