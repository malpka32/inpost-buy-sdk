<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Command;

use malpka32\InPostBuySdk\Collection\OfferCommandResultCollection;
use malpka32\InPostBuySdk\Dto\Offer\Command\OfferCommandResultDto;
use malpka32\InPostBuySdk\Mapper\CollectionMapperInterface;

/**
 * Maps raw batch command responses (list of { commandId, offerId, status })
 * into a typed OfferCommandResultCollection.
 *
 * @implements CollectionMapperInterface<OfferCommandResultCollection>
 */
final class OfferCommandResultCollectionMapper implements CollectionMapperInterface
{
    /**
     * @param array<string, mixed>|list<mixed> $data List of command results (or { data: [...] } wrapper)
     */
    public function map(array $data): OfferCommandResultCollection
    {
        $collection = new OfferCommandResultCollection();
        $items = $this->extractItems($data);
        foreach ($items as $item) {
            $collection->add(OfferCommandResultDto::fromArray($item));
        }

        return $collection;
    }

    /**
     * @param array<string, mixed>|list<mixed> $data
     * @return list<array<string, mixed>>
     */
    private function extractItems(array $data): array
    {
        $raw = array_is_list($data) ? $data : ($data['data'] ?? []);
        if (!is_array($raw)) {
            return [];
        }

        $items = [];
        foreach ($raw as $item) {
            if (is_array($item)) {
                /** @var array<string, mixed> $item */
                $items[] = $item;
            }
        }

        return $items;
    }
}
