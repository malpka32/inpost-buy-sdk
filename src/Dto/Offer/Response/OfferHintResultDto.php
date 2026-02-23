<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Response;

use malpka32\InPostBuySdk\Dto\Common\PageDto;
use malpka32\InPostBuySdk\Dto\Offer\Product\ProductHintDto;

/**
 * Result of offer hint – API response { page, data: ProductHint[] }.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/getOffersHintV1
 */
final class OfferHintResultDto
{
    /**
     * @param list<ProductHintDto> $items
     */
    public function __construct(
        private readonly PageDto $page,
        private readonly array $items,
    ) {
    }

    /**
     * @param array<string, mixed> $data Raw API response { page, data: ProductHint[] }
     */
    public static function fromArray(array $data): self
    {
        return new self(
            self::mapPage($data['page'] ?? null),
            self::mapItems($data['data'] ?? null),
        );
    }

    public function getPage(): PageDto
    {
        return $this->page;
    }

    /**
     * @return list<ProductHintDto>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    private static function mapPage(mixed $pageRaw): PageDto
    {
        if (!is_array($pageRaw)) {
            return new PageDto(0, 0, 0);
        }
        /** @var array<string, mixed> $pageRaw */
        return PageDto::fromArray($pageRaw);
    }

    /**
     * @param mixed $itemsRaw
     * @return list<ProductHintDto>
     */
    private static function mapItems(mixed $itemsRaw): array
    {
        if (!is_array($itemsRaw)) {
            return [];
        }

        $filtered = array_values(array_filter($itemsRaw, 'is_array'));
        /** @var list<array<string, mixed>> $filtered */
        return array_map(
            static fn (array $item): ProductHintDto => ProductHintDto::fromArray($item),
            $filtered,
        );
    }
}
