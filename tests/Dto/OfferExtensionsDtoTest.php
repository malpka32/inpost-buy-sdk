<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Dto;

use malpka32\InPostBuySdk\Dto\Offer\Command\OfferAttributePatchOperationDto;
use malpka32\InPostBuySdk\Dto\Offer\Command\OfferCommandResultDto;
use malpka32\InPostBuySdk\Dto\Offer\Command\OfferPriceUpdateDto;
use malpka32\InPostBuySdk\Dto\Offer\Command\OfferStockUpdateDto;
use malpka32\InPostBuySdk\Dto\Offer\Core\MoneyDto;
use malpka32\InPostBuySdk\Dto\Offer\Image\OfferImageDto;
use malpka32\InPostBuySdk\Dto\Offer\OfferAttributePatchOperationType;
use malpka32\InPostBuySdk\Dto\Offer\StockDto;
use PHPUnit\Framework\TestCase;

final class OfferExtensionsDtoTest extends TestCase
{
    public function testMoneyToArrayRoundsAmount(): void
    {
        $money = new MoneyDto(1.255, 'PLN');

        $this->assertSame(['amount' => 1.26, 'currency' => 'PLN'], $money->toArray());
    }

    public function testMoneyFromArrayUsesDefaults(): void
    {
        $money = MoneyDto::fromArray([]);

        $this->assertSame(0.0, $money->amount);
        $this->assertSame('PLN', $money->currency);
    }

    public function testPriceUpdateToArray(): void
    {
        $dto = new OfferPriceUpdateDto('offer-1', new MoneyDto(12.5, 'PLN'));

        $this->assertSame([
            'offerId' => 'offer-1',
            'price' => ['amount' => 12.5, 'currency' => 'PLN'],
        ], $dto->toArray());
    }

    public function testStockUpdateToArray(): void
    {
        $dto = new OfferStockUpdateDto('offer-2', new StockDto(1000, 'UNIT'));

        $this->assertSame([
            'offerId' => 'offer-2',
            'stock' => ['quantity' => 1000, 'unit' => 'UNIT'],
        ], $dto->toArray());
    }

    public function testCommandResultFromArray(): void
    {
        $dto = OfferCommandResultDto::fromArray([
            'commandId' => 'cmd-1',
            'offerId' => 'offer-3',
            'status' => 'PENDING',
        ]);

        $this->assertSame('cmd-1', $dto->commandId);
        $this->assertSame('offer-3', $dto->offerId);
        $this->assertSame('PENDING', $dto->status);
    }

    public function testAttributeUpsertOperationToArray(): void
    {
        $op = OfferAttributePatchOperationDto::upsert('attr-1', ['Red', 'Blue'], 'pl_PL');

        $this->assertSame([
            'type' => 'UPSERT',
            'id' => 'attr-1',
            'values' => ['Red', 'Blue'],
            'lang' => 'pl_PL',
        ], $op->toArray());
        $this->assertSame(OfferAttributePatchOperationType::UPSERT, $op->type);
    }

    public function testAttributeRemoveOperationToArray(): void
    {
        $op = OfferAttributePatchOperationDto::remove('attr-2');

        $this->assertSame([
            'type' => 'REMOVE',
            'id' => 'attr-2',
        ], $op->toArray());
    }

    public function testImageToArrayOmitsEmptyOptionals(): void
    {
        $image = new OfferImageDto('product.png');

        $this->assertSame(['fileName' => 'product.png'], $image->toArray());
    }

    public function testImageToArrayWithAllFields(): void
    {
        $image = new OfferImageDto('product.png', 'https://cdn.example/product.png', 1);

        $this->assertSame([
            'fileName' => 'product.png',
            'fileUrl' => 'https://cdn.example/product.png',
            'priority' => 1,
        ], $image->toArray());
    }
}
