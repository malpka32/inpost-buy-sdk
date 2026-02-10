<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Fixtures;

/**
 * API response mocks compliant with OpenAPI schema (src/make/openapi.json).
 * Used in unit tests instead of real system calls.
 */
final class ApiMocks
{
    /**
     * GET /v1/categories – category list (schema: Categories, array of Category).
     * Category: id, leaf, name, doesNotRequireGpsrInfo, description?, children?
     *
     * @return array<int, array<string, mixed>>
     */
    public static function categoriesResponse(): array
    {
        return [
            [
                'id' => '67909821-cc25-45ec-80ce-5ac4f2f01032',
                'leaf' => false,
                'name' => 'Consumer Electronics',
                'doesNotRequireGpsrInfo' => true,
                'description' => 'TVs, Audio, Notebooks and other stuff.',
            ],
            [
                'id' => '7f3b0598-5fd7-4cf6-8385-6a7cd44a6d74',
                'leaf' => false,
                'name' => 'Health',
                'doesNotRequireGpsrInfo' => true,
                'description' => 'Health products and all products related to healthy life.',
            ],
        ];
    }

    /**
     * Response with "categories" key (alternative form).
     *
     * @return array<string, mixed>
     */
    public static function categoriesResponseWithKey(): array
    {
        return ['categories' => self::categoriesResponse()];
    }

    /**
     * Response with "items" key (flattened, with parentId).
     *
     * @return array<string, mixed>
     */
    public static function categoriesResponseItemsWithParentId(): array
    {
        return [
            'items' => [
                ['id' => 'root-1', 'name' => 'Root', 'parentId' => null],
                ['id' => 'child-1', 'name' => 'Child', 'parent_id' => 'root-1'],
            ],
        ];
    }

    /**
     * GET /v1/organizations/{id}/offers – offers list (schema: Offers).
     * Offers: page?, data: OfferDetails[]. OfferDetails: metadata, offer. Offer: id, status, product, stock, price.
     *
     * @return array<string, mixed>
     */
    public static function offersListResponse(): array
    {
        return [
            'page' => ['limit' => 10, 'offset' => 0, 'total' => 1],
            'data' => [
                [
                    'metadata' => ['source' => 'test'],
                    'offer' => self::singleOfferPayload(),
                ],
            ],
        ];
    }

    /**
     * Single Offer object (schema: Offer) – id, status, product, stock, price.
     *
     * @return array<string, mixed>
     */
    public static function singleOfferPayload(): array
    {
        return [
            'id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
            'status' => 'PUBLISHED',
            'externalId' => 'SKU-001',
            'product' => [
                'name' => 'Test Product',
                'description' => 'Description',
                'brand' => 'Inne',
                'categoryId' => '67909821-cc25-45ec-80ce-5ac4f2f01032',
                'sku' => 'SKU-001',
                'ean' => '5904959447006',
                'attributes' => [
                    ['id' => 'attr-color', 'values' => ['Red'], 'lang' => 'en'],
                    ['id' => 'attr-size', 'values' => ['M', 'L']],
                ],
                'dimension' => [
                    'width' => 200,
                    'height' => 100,
                    'length' => 300,
                    'weight' => 340,
                ],
            ],
            'stock' => ['quantity' => 10, 'unit' => 'UNIT'],
            'price' => [
                'grossPrice' => ['amount' => 99.99, 'currency' => 'PLN'],
                'taxRateInfo' => '23%',
            ],
        ];
    }

    /**
     * Offer without optional fields (minimal payload).
     *
     * @return array<string, mixed>
     */
    public static function minimalOfferPayload(): array
    {
        return [
            'id' => 'minimal-offer-id',
            'status' => 'PENDING',
            'product' => [
                'name' => 'Minimal',
                'description' => '',
                'brand' => 'Inne',
                'categoryId' => 'cat-id',
            ],
            'stock' => ['quantity' => 0, 'unit' => 'UNIT'],
            'price' => ['grossPrice' => ['amount' => 0.0, 'currency' => 'PLN'], 'taxRateInfo' => '23%'],
        ];
    }

    /**
     * POST Offer – OfferCreated response (schema: offerId, commandId).
     *
     * @return array<string, mixed>
     */
    public static function offerCreatedResponse(): array
    {
        return [
            'commandId' => 'cmd-uuid-123',
            'offerId' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
            'externalId' => 'SKU-001',
        ];
    }

    /**
     * Batch Offers Created – array of OfferCreated.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function batchOffersCreatedResponse(): array
    {
        return [
            ['commandId' => 'cmd-1', 'offerId' => 'offer-uuid-1', 'externalId' => 'EXT-1'],
            ['commandId' => 'cmd-2', 'offerId' => 'offer-uuid-2', 'externalId' => 'EXT-2'],
        ];
    }

    /**
     * GET Orders – orders list (schema: Orders – page?, data: Order[]).
     * Order: id, organizationId, createdAt, status, delivery, orderLines, finalPrice, paymentDetails.
     *
     * @return array<string, mixed>
     */
    public static function ordersListResponse(): array
    {
        return [
            'page' => ['limit' => 10, 'offset' => 0, 'total' => 1],
            'items' => [self::singleOrderPayload()],
        ];
    }

    /**
     * Alternative form with "orders" key.
     *
     * @return array<string, mixed>
     */
    public static function ordersListResponseWithOrdersKey(): array
    {
        return [
            'orders' => [self::singleOrderPayload()],
        ];
    }

    /**
     * Single Order (schema: Order).
     *
     * @return array<string, mixed>
     */
    public static function singleOrderPayload(): array
    {
        return [
            'id' => 'order-uuid-123',
            'organizationId' => 'org-uuid',
            'createdAt' => '2025-02-15T13:45:30+00:00',
            'updatedAt' => '2025-02-15T14:00:00+00:00',
            'status' => 'CREATED',
            'delivery' => [],
            'orderLines' => [],
            'finalPrice' => ['amount' => 99.99, 'currency' => 'PLN'],
            'paymentDetails' => [],
            'reference' => 'REF-001',
        ];
    }

    /**
     * ErrorResponse (application/problem+json) – schema: errorCode, errorMessage?, details?.
     *
     * @return array<string, mixed>
     */
    public static function errorResponsePayload(): array
    {
        return [
            'errorCode' => 'RESOURCE_NOT_FOUND',
            'errorMessage' => 'The requested resource was not found.',
            'details' => [
                ['field' => '#/id', 'detail' => 'Order not found'],
            ],
        ];
    }

    /**
     * ErrorResponse without details.
     *
     * @return array<string, mixed>
     */
    public static function errorResponsePayloadMinimal(): array
    {
        return [
            'errorCode' => 'FORBIDDEN',
            'errorMessage' => "You're not allowed to perform this action.",
        ];
    }

    /**
     * AttributeValue (schema) – id, values, lang?.
     *
     * @return array<string, mixed>
     */
    public static function attributeValuePayload(): array
    {
        return [
            'id' => 'attr-uuid',
            'values' => ['Value1', 'Value2'],
            'lang' => 'pl',
        ];
    }

    /**
     * Dimension (schema) – width, height, length, weight (int32 mm/g).
     *
     * @return array<string, mixed>
     */
    public static function dimensionPayload(): array
    {
        return [
            'width' => 200,
            'height' => 100,
            'length' => 300,
            'weight' => 340,
        ];
    }
}
