<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Response;

/**
 * Result of list offer events – API response { data: OfferEvent[] }.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/getOffersEventsV1
 */
final class OfferEventsResultDto
{
    /**
     * @param list<OfferEventDto> $events
     */
    public function __construct(
        private readonly array $events,
    ) {
    }

    /**
     * @param array<string, mixed> $data Raw API response { data: OfferEvent[] }
     */
    public static function fromArray(array $data): self
    {
        return new self(self::mapEvents($data['data'] ?? null));
    }

    /**
     * @return list<OfferEventDto>
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param mixed $itemsRaw
     * @return list<OfferEventDto>
     */
    private static function mapEvents(mixed $itemsRaw): array
    {
        if (!is_array($itemsRaw)) {
            return [];
        }

        $filtered = array_values(array_filter($itemsRaw, 'is_array'));
        /** @var list<array<string, mixed>> $filtered */
        return array_map(
            static fn (array $item): OfferEventDto => OfferEventDto::fromArray($item),
            $filtered,
        );
    }
}
