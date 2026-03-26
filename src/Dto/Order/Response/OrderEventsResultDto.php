<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Response;

use malpka32\InPostBuySdk\Collection\OrderEventCollection;

/**
 * Result of list order events – API response { data: OrderEvent[] }.
 */
final class OrderEventsResultDto
{
    /**
     * @param OrderEventCollection $events
     */
    public function __construct(
        private readonly OrderEventCollection $events,
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
     * @return OrderEventCollection
     */
    public function getEvents(): OrderEventCollection
    {
        return $this->events;
    }

    /**
     * @param mixed $itemsRaw
     * @return OrderEventCollection
     */
    private static function mapEvents(mixed $itemsRaw): OrderEventCollection
    {
        if (!is_array($itemsRaw)) {
            return new OrderEventCollection();
        }

        $filtered = array_values(array_filter($itemsRaw, 'is_array'));
        /** @var list<array<string, mixed>> $filtered */
        return OrderEventCollection::fromArray(array_map(
            static fn (array $item): OrderEventDto => OrderEventDto::fromArray($item),
            $filtered,
        ));
    }
}
