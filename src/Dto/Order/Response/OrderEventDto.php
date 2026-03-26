<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Response;

use malpka32\InPostBuySdk\Dto\Order\OrderEventType;
use malpka32\InPostBuySdk\Helper\DateTimeHelper;

/**
 * Single order event from API.
 */
final class OrderEventDto
{
    /**
     * @param array<string, mixed> $raw Raw event payload from API
     */
    public function __construct(
        public ?string $id,
        public ?OrderEventOrderDto $order,
        public ?OrderEventType $eventType,
        public ?\DateTimeInterface $occurredAt,
        private readonly array $raw,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $idRaw = $data['id'] ?? null;
        $orderRaw = $data['order'] ?? null;
        $eventTypeRaw = $data['orderEventType'] ?? null;
        $occurredAtRaw = $data['occurredAt'] ?? null;

        return new self(
            id: $idRaw === null ? null : (string) $idRaw,
            order: is_array($orderRaw) ? OrderEventOrderDto::fromArray($orderRaw) : null,
            eventType: is_string($eventTypeRaw) ? OrderEventType::tryFrom($eventTypeRaw) : null,
            occurredAt: DateTimeHelper::parseOrNull($occurredAtRaw),
            raw: $data,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->raw;
    }

}
