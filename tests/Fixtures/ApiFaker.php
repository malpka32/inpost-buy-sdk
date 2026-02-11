<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Fixtures;

use Faker\Factory;
use Faker\Generator;

/**
 * Generates varied API data (OpenAPI-compliant) using Faker.
 * Useful for verifying mapping with different values in tests.
 */
final class ApiFaker
{
    private Generator $faker;

    public function __construct(?Generator $faker = null)
    {
        $this->faker = $faker ?? Factory::create('pl_PL');
    }

    /**
     * Category list (schema: Category[]) – random names and descriptions.
     *
     * @return array<int, array<string, mixed>>
     */
    public function categories(int $count = 3): array
    {
        $out = [];
        for ($i = 0; $i < $count; $i++) {
            $out[] = [
                'id' => $this->faker->uuid(),
                'leaf' => $this->faker->boolean(30),
                'name' => $this->faker->words(2, true),
                'doesNotRequireGpsrInfo' => $this->faker->boolean(70),
                'description' => $this->faker->sentence(6),
            ];
        }
        return $out;
    }

    /**
     * Categories response with key (e.g. 'categories' or 'items').
     *
     * @return array<string, mixed>
     */
    public function categoriesResponse(string $key = 'categories', int $count = 3): array
    {
        return [$key => $this->categories($count)];
    }

    /**
     * Single Offer (product, stock, price) – random values.
     *
     * @return array<string, mixed>
     */
    public function singleOffer(): array
    {
        $productName = $this->faker->words(3, true);
        $sku = strtoupper($this->faker->bothify('???-###'));
        $quantity = $this->faker->numberBetween(0, 999);
        $amount = $this->faker->randomFloat(2, 1, 9999);

        $attrCount = $this->faker->numberBetween(0, 3);
        $attributes = [];
        for ($i = 0; $i < $attrCount; $i++) {
            $attributes[] = [
                'id' => $this->faker->uuid(),
                'values' => $this->faker->randomElements(['S', 'M', 'L', 'XL', 'Czerwony', 'Niebieski', 'Czarny'], $this->faker->numberBetween(1, 3)),
                'lang' => $this->faker->optional(0.7)->randomElement(['pl', 'en']),
            ];
        }

        $hasDimension = $this->faker->boolean(80);
        $dimension = $hasDimension ? [
            'width' => $this->faker->numberBetween(50, 500),
            'height' => $this->faker->numberBetween(50, 400),
            'length' => $this->faker->numberBetween(100, 600),
            'weight' => $this->faker->numberBetween(100, 5000),
        ] : [];

        $product = [
            'name' => $productName,
            'description' => $this->faker->paragraph(2),
            'brand' => 'Inne',
            'categoryId' => $this->faker->uuid(),
            'sku' => $sku,
            'ean' => $this->faker->optional(0.8)->ean13(),
            'attributes' => $attributes,
            'dimension' => $dimension,
        ];
        $product = array_filter($product, fn ($v) => $v !== [] && $v !== null);

        return [
            'id' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(['PENDING', 'PUBLISHED', 'REJECTED', 'CLOSED', 'SOLDOUT']),
            'externalId' => $sku,
            'product' => $product,
            'stock' => ['quantity' => $quantity, 'unit' => 'UNIT'],
            'price' => [
                'grossPrice' => ['amount' => $amount, 'currency' => 'PLN'],
                'taxRateInfo' => '23%',
            ],
        ];
    }

    /**
     * Offers list (Offers) – page + data with random offers.
     *
     * @return array<string, mixed>
     */
    public function offersList(int $count = 5, ?int $total = null): array
    {
        $total = $total ?? $count;
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'metadata' => ['source' => 'faker'],
                'offer' => $this->singleOffer(),
            ];
        }
        return [
            'page' => [
                'limit' => 10,
                'offset' => 0,
                'total' => $total,
            ],
            'data' => $data,
        ];
    }

    /**
     * Single Order – random id, status, dates, amount.
     *
     * @return array<string, mixed>
     */
    public function singleOrder(): array
    {
        $created = $this->faker->dateTimeBetween('-30 days', 'now');
        $updated = $this->faker->dateTimeBetween($created, 'now');
        return [
            'id' => $this->faker->uuid(),
            'organizationId' => $this->faker->uuid(),
            'createdAt' => $created->format(\DateTimeInterface::ATOM),
            'updatedAt' => $updated->format(\DateTimeInterface::ATOM),
            'status' => $this->faker->randomElement(['CREATED', 'ACCEPTED', 'CANCELED', 'REFUSED', 'REJECTED']),
            'delivery' => [],
            'orderLines' => [],
            'finalPrice' => [
                'amount' => $this->faker->randomFloat(2, 10, 2000),
                'currency' => 'PLN',
            ],
            'paymentDetails' => [],
            'reference' => $this->faker->optional(0.8)->regexify('REF-[A-Z0-9]{6,12}'),
        ];
    }

    /**
     * Orders list (items or orders).
     *
     * @return array<string, mixed>
     */
    public function ordersList(int $count = 3, string $key = 'items'): array
    {
        $orders = [];
        for ($i = 0; $i < $count; $i++) {
            $orders[] = $this->singleOrder();
        }
        return [
            'page' => ['limit' => 10, 'offset' => 0, 'total' => $count],
            $key => $orders,
        ];
    }

    /**
     * OfferCreated – random offer id and commandId.
     *
     * @return array<string, mixed>
     */
    public function offerCreated(): array
    {
        return [
            'commandId' => $this->faker->uuid(),
            'offerId' => $this->faker->uuid(),
            'externalId' => strtoupper($this->faker->bothify('???-###')),
        ];
    }

    /**
     * Batch OfferCreated – list with random ids.
     *
     * @return list<array<string, mixed>>
     */
    public function batchOffersCreated(int $count = 3): array
    {
        $out = [];
        for ($i = 0; $i < $count; $i++) {
            $out[] = $this->offerCreated();
        }
        return $out;
    }

    /**
     * ErrorResponse – random code and message.
     *
     * @return array<string, mixed>
     */
    public function errorResponse(?string $code = null, ?string $message = null): array
    {
        $codes = ['RESOURCE_NOT_FOUND', 'FORBIDDEN', 'BAD_REQUEST', 'VALIDATION_ERROR'];
        $payload = [
            'errorCode' => $code ?? $this->faker->randomElement($codes),
            'errorMessage' => $message ?? $this->faker->sentence(4),
        ];
        if ($this->faker->boolean(50)) {
            $payload['details'] = [
                ['field' => '#/' . $this->faker->word(), 'detail' => $this->faker->sentence(3)],
            ];
        }
        return $payload;
    }

    /**
     * AttributeValue – random id, values, optionally lang.
     *
     * @return array<string, mixed>
     */
    public function attributeValue(): array
    {
        $values = $this->faker->randomElements(
            ['S', 'M', 'L', 'Czerwony', 'Niebieski', 'Tak', 'Nie'],
            $this->faker->numberBetween(1, 3)
        );
        $item = [
            'id' => $this->faker->uuid(),
            'values' => $values,
        ];
        if ($this->faker->boolean(60)) {
            $item['lang'] = $this->faker->randomElement(['pl', 'en']);
        }
        return $item;
    }

    /**
     * Dimension – random dimensions in range (mm, g).
     *
     * @return array<string, mixed>
     */
    public function dimension(): array
    {
        return [
            'width' => $this->faker->numberBetween(50, 500),
            'height' => $this->faker->numberBetween(50, 400),
            'length' => $this->faker->numberBetween(100, 600),
            'weight' => $this->faker->numberBetween(100, 5000),
        ];
    }

    /**
     * Creates OfferDto with random data – for offer send tests.
     */
    public function createOfferDto(): \malpka32\InPostBuySdk\Dto\OfferDto
    {
        $offer = $this->singleOffer();
        $product = $offer['product'];
        $stock = $offer['stock'];
        $price = $offer['price'];
        $attrs = new \malpka32\InPostBuySdk\Collection\AttributeValueCollection();
        foreach ($product['attributes'] ?? [] as $a) {
            $attrs->add(new \malpka32\InPostBuySdk\Dto\AttributeValueDto(
                $a['id'],
                array_map('strval', $a['values']),
                $a['lang'] ?? null
            ));
        }
        $dim = !empty($product['dimension'])
            ? new \malpka32\InPostBuySdk\Dto\DimensionDto(
                $product['dimension']['width'],
                $product['dimension']['height'],
                $product['dimension']['length'],
                $product['dimension']['weight']
            )
            : null;
        $productDto = new \malpka32\InPostBuySdk\Dto\ProductDto(
            $product['name'],
            $product['description'],
            $product['brand'],
            $product['categoryId'],
            $product['sku'] ?? null,
            $product['ean'] ?? null,
            $attrs,
            $dim
        );
        $stockDto = new \malpka32\InPostBuySdk\Dto\StockDto($stock['quantity'], $stock['unit']);
        $gross = $price['grossPrice'];
        $priceDto = new \malpka32\InPostBuySdk\Dto\PriceDto(
            $gross['amount'],
            $gross['currency'],
            $price['taxRateInfo']
        );
        return new \malpka32\InPostBuySdk\Dto\OfferDto(
            $offer['externalId'],
            $productDto,
            $stockDto,
            $priceDto
        );
    }

    public function getFaker(): Generator
    {
        return $this->faker;
    }
}
