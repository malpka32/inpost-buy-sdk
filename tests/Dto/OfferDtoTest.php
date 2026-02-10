<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Dto;

use malpka32\InPostBuySdk\Collection\AttributeValueCollection;
use malpka32\InPostBuySdk\Dto\AttributeValueDto;
use malpka32\InPostBuySdk\Dto\DimensionDto;
use malpka32\InPostBuySdk\Dto\OfferDto;
use malpka32\InPostBuySdk\Dto\PriceDto;
use malpka32\InPostBuySdk\Dto\ProductDto;
use malpka32\InPostBuySdk\Dto\StockDto;
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
}
