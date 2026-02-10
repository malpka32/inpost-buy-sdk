<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper;

use malpka32\InPostBuySdk\Dto\OrderDto;

/**
 * Mapuje odpowiedź API na OrderDto / listę OrderDto.
 */
final class OrderResponseMapper
{
    /**
     * @return OrderDto[]
     */
    public function mapList(array $data): array
    {
        $list = $data['items'] ?? $data['orders'] ?? (isset($data['id']) ? [$data] : []);
        $out = [];
        foreach ($list as $item) {
            $out[] = $this->mapItem($item);
        }
        return $out;
    }

    public function mapItem(array $item): OrderDto
    {
        $createdAt = isset($item['created_at'])
            ? \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $item['created_at'])
            : null;
        $updatedAt = isset($item['updated_at'])
            ? \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $item['updated_at'])
            : null;

        return new OrderDto(
            inpostOrderId: (string) ($item['id'] ?? $item['order_id'] ?? ''),
            status: $item['status'] ?? null,
            reference: $item['reference'] ?? null,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            items: $item['items'] ?? null,
            raw: $item,
        );
    }
}
