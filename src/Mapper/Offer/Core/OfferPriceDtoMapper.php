<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Core;

use malpka32\InPostBuySdk\Dto\Offer\Deposit\DepositPositionDto;
use malpka32\InPostBuySdk\Dto\Offer\PriceDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;
use malpka32\InPostBuySdk\Mapper\Offer\Deposit\OfferDepositPositionCollectionMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Deposit\OfferDepositPositionDtoMapper;

/**
 * @implements ItemMapperInterface<PriceDto>
 */
final class OfferPriceDtoMapper implements ItemMapperInterface
{
    private readonly OfferDepositPositionCollectionMapper $depositsMapper;

    /**
     * @param ItemMapperInterface<DepositPositionDto> $depositPositionMapper
     */
    public function __construct(
        ItemMapperInterface $depositPositionMapper = new OfferDepositPositionDtoMapper(),
    ) {
        $this->depositsMapper = new OfferDepositPositionCollectionMapper($depositPositionMapper);
    }

    public function canProcess(array $item): bool
    {
        return true;
    }

    public function mapItem(mixed $item): PriceDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        $grossPrice = $this->extractGrossPriceRaw($item);
        /** @var array<string, mixed> $grossPrice */

        return new PriceDto(
            amount: ArrayHelper::asFloat($grossPrice['amount'] ?? 0.0),
            currency: ArrayHelper::asString(ArrayHelper::get($grossPrice, 'currency') ?? 'PLN'),
            taxRateInfo: ArrayHelper::asString(ArrayHelper::get($item, 'taxRateInfo') ?? '23%'),
            deposits: $this->depositsMapper->map($item['deposits'] ?? null),
        );
    }

    /**
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    private function extractGrossPriceRaw(array $item): array
    {
        $raw = $item['grossPrice'] ?? null;
        if (!is_array($raw)) {
            return [];
        }
        /** @var array<string, mixed> $raw */
        return $raw;
    }
}
