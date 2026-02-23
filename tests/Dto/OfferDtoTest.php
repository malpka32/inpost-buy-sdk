<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Dto;

use malpka32\InPostBuySdk\Collection\AttributeValueCollection;
use malpka32\InPostBuySdk\Dto\Attribute\AttributeValueDto;
use malpka32\InPostBuySdk\Dto\Offer\Deposit\DepositPositionDto;
use malpka32\InPostBuySdk\Dto\Offer\Deposit\DepositTypeDto;
use malpka32\InPostBuySdk\Dto\Offer\FeaturesDto;
use malpka32\InPostBuySdk\Dto\Offer\OfferDto;
use malpka32\InPostBuySdk\Dto\Offer\PostSaleDto;
use malpka32\InPostBuySdk\Dto\Offer\PriceDto;
use malpka32\InPostBuySdk\Dto\Offer\Product\DimensionDto;
use malpka32\InPostBuySdk\Dto\Offer\Product\ProductDto;
use malpka32\InPostBuySdk\Dto\Offer\ShippingTimeDto;
use malpka32\InPostBuySdk\Dto\Offer\StockDto;
use PHPUnit\Framework\TestCase;

final class OfferDtoTest extends TestCase
{
    public function testToArrayProducesOfferProposalStructure(): void
    {
        $attrs = new AttributeValueCollection();
        $attrs->add(new AttributeValueDto('attr-1', ['Red'], 'en'));
        $product = new ProductDto(
            'Product',
            'Desc',
            'MyBrand',
            'cat-uuid',
            sku: 'SKU-001',
            ean: '5901234567890',
            attributes: $attrs,
            dimension: new DimensionDto(200, 100, 300, 340),
        );
        $stock = new StockDto(5, 'UNIT');
        $price = new PriceDto(99.99, 'PLN', '23%');
        $dto = new OfferDto('SKU-001', $product, $stock, $price);

        $payload = $dto->toArray();

        $this->assertSame('SKU-001', $payload['externalId']);
        $this->assertSame('Product', $payload['product']['name']);
        $this->assertSame('MyBrand', $payload['product']['brand']);
        $this->assertSame(5, $payload['stock']['quantity']);
        $this->assertSame('UNIT', $payload['stock']['unit']);
        $this->assertSame(99.99, $payload['price']['grossPrice']['amount']);
        $this->assertSame('PLN', $payload['price']['grossPrice']['currency']);
        $this->assertSame('23%', $payload['price']['taxRateInfo']);
    }

    public function testToArrayWithoutOptionals(): void
    {
        $product = new ProductDto('A', '', 'B', 'cat');
        $stock = new StockDto(0, 'PAIR');
        $price = new PriceDto(10.5, 'EUR', '8%');
        $dto = new OfferDto('ext-1', $product, $stock, $price);

        $payload = $dto->toArray();

        $this->assertSame('PAIR', $payload['stock']['unit']);
        $this->assertSame('EUR', $payload['price']['grossPrice']['currency']);
        $this->assertSame('8%', $payload['price']['taxRateInfo']);
    }

    public function testToArrayWithOptionalFields(): void
    {
        $product = new ProductDto('Prod', 'Desc', 'Brand', 'cat', model: 'Model X', superModel: 'Super');
        $stock = new StockDto(1, 'UNIT');
        $deposit = new DepositPositionDto(2, new DepositTypeDto('dep-uuid', 1.0, 'PLN'));
        $price = new PriceDto(50.0, 'PLN', '23%', deposits: [$deposit]);
        $dto = new OfferDto(
            'ext-2',
            $product,
            $stock,
            $price,
            shippingTime: new ShippingTimeDto(3),
            affiliationProductUrl: 'https://shop.example.com/prod',
            postSale: new PostSaleDto('Return policy', 'Complaint policy'),
            features: new FeaturesDto(refundable: false),
        );

        $payload = $dto->toArray();

        $this->assertSame('Model X', $payload['product']['model']);
        $this->assertSame('Super', $payload['product']['superModel']);
        $this->assertArrayHasKey('deposits', $payload['price']);
        $this->assertCount(1, $payload['price']['deposits']);
        $this->assertSame(2, $payload['price']['deposits'][0]['quantity']);
        $this->assertSame('dep-uuid', $payload['price']['deposits'][0]['depositType']['id']);
        $this->assertSame(3, $payload['shippingTime']['daysToShip']);
        $this->assertSame('https://shop.example.com/prod', $payload['affiliationProductUrl']);
        $this->assertSame('Return policy', $payload['postSale']['returnPolicy']['description']);
        $this->assertSame('Complaint policy', $payload['postSale']['complaintPolicy']['description']);
        $this->assertFalse($payload['features']['refundable']);
    }
}
