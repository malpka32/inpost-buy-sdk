<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Client;

use malpka32\InPostBuySdk\Dto\AttributeValueDto;
use malpka32\InPostBuySdk\Dto\OfferDto;

/**
 * Buduje payload OfferProposal (OpenAPI) z OfferDto.
 * Jedna odpowiedzialność: transformacja DTO → tablica do API.
 */
final class OfferProposalBuilder
{
    private const BRAND_DEFAULT = 'Inne';
    private const STOCK_UNIT = 'UNIT';
    private const CURRENCY = 'PLN';
    private const TAX_RATE = '23%';

    public function build(OfferDto $dto): array
    {
        return [
            'externalId' => $this->resolveExternalId($dto),
            'product' => $this->buildProduct($dto),
            'stock' => $this->buildStock($dto),
            'price' => $this->buildPrice($dto),
        ];
    }

    private function resolveExternalId(OfferDto $dto): string
    {
        if ($dto->sku !== null && $dto->sku !== '') {
            return $dto->sku;
        }
        return 'ps-' . $dto->idProduct . '-' . $dto->idProductAttribute;
    }

    /** @return array<string, mixed> */
    private function buildProduct(OfferDto $dto): array
    {
        $product = [
            'name' => $dto->name ?? '',
            'description' => $dto->description ?? '',
            'brand' => self::BRAND_DEFAULT,
            'categoryId' => $dto->inpostCategoryId ?? '',
        ];
        if ($dto->sku !== null && $dto->sku !== '') {
            $product['sku'] = $dto->sku;
        }
        if ($dto->ean !== null && $dto->ean !== '') {
            $product['ean'] = $dto->ean;
        }
        if ($dto->attributes !== null && $dto->attributes !== []) {
            $product['attributes'] = $this->attributesToArray($dto->attributes);
        }
        if ($dto->dimension !== null) {
            $product['dimension'] = [
                'width' => $dto->dimension->width,
                'height' => $dto->dimension->height,
                'length' => $dto->dimension->length,
                'weight' => $dto->dimension->weight,
            ];
        }
        return $product;
    }

    /** @param list<AttributeValueDto> $attributes */
    private function attributesToArray(array $attributes): array
    {
        return array_map(
            function (AttributeValueDto $a): array {
                $item = ['id' => $a->id, 'values' => $a->values];
                if ($a->lang !== null && $a->lang !== '') {
                    $item['lang'] = $a->lang;
                }
                return $item;
            },
            $attributes
        );
    }

    /** @return array{quantity: int, unit: string} */
    private function buildStock(OfferDto $dto): array
    {
        return [
            'quantity' => (int) max(0, $dto->quantity),
            'unit' => self::STOCK_UNIT,
        ];
    }

    /** @return array{grossPrice: array{amount: float, currency: string}, taxRateInfo: string} */
    private function buildPrice(OfferDto $dto): array
    {
        return [
            'grossPrice' => [
                'amount' => round((float) $dto->priceGross, 2),
                'currency' => self::CURRENCY,
            ],
            'taxRateInfo' => self::TAX_RATE,
        ];
    }
}
