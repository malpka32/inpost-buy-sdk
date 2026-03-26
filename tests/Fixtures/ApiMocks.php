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
     * GET /v1/categories – flat category list (API: {"categories": [{"id","name","parentId"}, ...]}).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function categoriesResponse(): array
    {
        return [
            [
                'id' => '67909821-cc25-45ec-80ce-5ac4f2f01032',
                'name' => 'Consumer Electronics',
                'parentId' => null,
            ],
            [
                'id' => '7f3b0598-5fd7-4cf6-8385-6a7cd44a6d74',
                'name' => 'Health',
                'parentId' => null,
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
     * Response with "categories" key and explicit parentId values.
     *
     * @return array<string, mixed>
     */
    public static function categoriesResponseItemsWithParentId(): array
    {
        return [
            'categories' => [
                ['id' => 'root-1', 'name' => 'Root', 'parentId' => null],
                ['id' => 'child-1', 'name' => 'Child', 'parentId' => 'root-1'],
            ],
        ];
    }

    /**
     * API tree format – root array with nested children (InPost GET /v1/categories response).
     *
     * @return list<array<string, mixed>>
     */
    public static function categoriesTreeResponse(): array
    {
        return [
            [
                'id' => 'root-uuid',
                'name' => 'Electronics',
                'leaf' => false,
                'doesNotRequireGpsrInfo' => true,
                'children' => [
                    [
                        'id' => 'child-uuid',
                        'name' => 'Phones',
                        'leaf' => true,
                        'doesNotRequireGpsrInfo' => true,
                        'children' => [],
                    ],
                ],
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
     * Offer with optional fields (model, superModel, shippingTime, postSale, features).
     *
     * @return array<string, mixed>
     */
    public static function offerWithOptionalFieldsPayload(): array
    {
        return [
            'id' => 'offer-with-optional',
            'status' => 'PUBLISHED',
            'externalId' => 'EXT-OPT',
            'product' => [
                'name' => 'Prod z opcjami',
                'description' => 'Opis',
                'brand' => 'Brand',
                'categoryId' => 'cat-uuid',
                'sku' => 'SKU-OPT',
                'model' => 'Model123',
                'superModel' => 'SuperModel',
                'manufacturerProductNumber' => 'MPN-001',
                'attributes' => [],
            ],
            'stock' => ['quantity' => 5, 'unit' => 'UNIT'],
            'price' => ['grossPrice' => ['amount' => 49.99, 'currency' => 'PLN'], 'taxRateInfo' => '23%'],
            'shippingTime' => ['daysToShip' => 2],
            'affiliationProductUrl' => 'https://shop.example.com/product',
            'postSale' => [
                'returnPolicy' => ['description' => 'Zwrot 14 dni'],
                'complaintPolicy' => ['description' => 'Reklamacja 24m'],
            ],
            'features' => ['refundable' => true],
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
            'data' => [self::singleOrderPayload()],
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
            'customer' => [
                'email' => 'buyer@example.com',
                'firstName' => 'Jan',
                'lastName' => 'Kowalski',
                'phoneNumber' => '+48111111111',
                'address' => [
                    'street' => 'Testowa',
                    'city' => 'Krakow',
                    'postCode' => '31-001',
                    'state' => 'malopolskie',
                    'countryCode' => 'PL',
                    'building' => '10',
                    'flat' => '2',
                ],
            ],
            'invoice' => [
                'email' => 'billing@example.com',
                'legalForm' => 'COMPANY',
                'companyName' => 'ACME Sp. z o.o.',
                'firstName' => 'Jan',
                'lastName' => 'Kowalski',
                'taxIdPrefix' => 'PL',
                'taxId' => '1234567890',
                'address' => [
                    'street' => 'Fakturowa',
                    'city' => 'Warszawa',
                    'postCode' => '00-001',
                    'state' => 'mazowieckie',
                    'countryCode' => 'PL',
                    'building' => '1A',
                    'flat' => null,
                ],
            ],
            'delivery' => [
                'deliveryType' => 'APM',
                'parcels' => [
                    [
                        'trackingNumber' => 'PKG123456789',
                        'createdAt' => '2025-02-15T13:50:00+00:00',
                        'status' => 'SENT',
                    ],
                ],
                'name' => 'Paczkomat',
                'deliveryPoint' => 'KRA01M',
                'address' => [
                    'street' => 'Punktowa',
                    'city' => 'Krakow',
                    'postCode' => '30-001',
                    'state' => 'malopolskie',
                    'countryCode' => 'PL',
                    'building' => '5',
                    'flat' => null,
                ],
                'email' => 'delivery@example.com',
                'phoneNumber' => '+48222222222',
                'price' => ['amount' => 12.99, 'currency' => 'PLN'],
                'expectedDeliveryDate' => '2025-02-17T10:00:00+00:00',
            ],
            'orderLines' => [
                [
                    'offer' => [
                        'offerId' => 'offer-uuid-1',
                        'product' => [
                            'productId' => 'prod-1',
                            'name' => 'Test product',
                            'ean' => '5904959447006',
                            'sku' => 'SKU-1',
                        ],
                        'price' => ['amount' => 49.99, 'currency' => 'PLN'],
                        'basePrice' => ['amount' => 59.99, 'currency' => 'PLN'],
                        'promotionPrice' => ['amount' => 49.99, 'currency' => 'PLN'],
                        'externalId' => 'EXT-SKU-1',
                    ],
                ],
            ],
            'finalPrice' => ['amount' => 99.99, 'currency' => 'PLN'],
            'basePrice' => ['amount' => 109.99, 'currency' => 'PLN'],
            'promotionPrice' => ['amount' => 99.99, 'currency' => 'PLN'],
            'paymentDetails' => [
                'selectedPaymentType' => 'CARD',
                'payments' => [
                    [
                        'paymentId' => 'payment-1',
                        'paymentType' => 'CARD',
                        'paymentDate' => '2025-02-15T13:46:00+00:00',
                    ],
                ],
            ],
            'reference' => 'REF-001',
            'comment' => 'Please ring the bell',
        ];
    }

    /**
     * GET /v1/organizations/{id}/orders/events – events list response.
     *
     * @return array<string, mixed>
     */
    public static function orderEventsResponse(): array
    {
        return [
            'data' => [
                self::singleOrderEventPayload(),
            ],
        ];
    }

    /**
     * Single Order event payload.
     *
     * @return array<string, mixed>
     */
    public static function singleOrderEventPayload(): array
    {
        return [
            'id' => 'evt-uuid-1',
            'order' => [
                'id' => 'order-uuid-123',
            ],
            'orderEventType' => 'CREATED',
            'occurredAt' => '2025-02-15T13:45:30+00:00',
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
     * GET Offer Attachments – list response (schema: page, data: Attachment[]).
     *
     * @return array<string, mixed>
     */
    public static function attachmentsListResponse(): array
    {
        return [
            'page' => ['limit' => 10, 'offset' => 0, 'total' => 1],
            'data' => [self::attachmentPayload()],
        ];
    }

    /**
     * Single Attachment (schema: id, name, attachmentType, createdAt, url).
     *
     * @return array<string, mixed>
     */
    public static function attachmentPayload(): array
    {
        return [
            'id' => 'att-uuid-123',
            'name' => 'product.jpg',
            'attachmentType' => 'IMAGE',
            'createdAt' => '2025-02-15T13:45:30',
            'url' => 'https://example.com/api/attachments/product.jpg',
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
