<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Response;

/**
 * Result of list order events – API response { data: OrderEvent[] }.
 */
final class OrderEventsResultDto
{
    /**
     * @param list<OrderEventDto> $events
     */
    public function __construct(
        private readonly array $events,
    ) {
    }

    /**
     * @param array<string, mixed> $data Raw API response { data: OrderEvent[] }
     */
    public static function fromArray(array $data): self
    {
        return new self(self::mapEvents($data['data'] ?? null));
    }

    /**
     * @return list<OrderEventDto>
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param mixed $itemsRaw
     * @return list<OrderEventDto>
     */
    private static function mapEvents(mixed $itemsRaw): array
    {
        if (!is_array($itemsRaw)) {
            return [];
        }

        $filtered = array_values(array_filter($itemsRaw, 'is_array'));
        /** @var list<array<string, mixed>> $filtered */
        return array_map(
            static fn (array $item): OrderEventDto => OrderEventDto::fromArray($item),
            $filtered,
        );
    }
}
