<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

/**
 * Categories endpoint contract – enables testing with fake data.
 */
interface CategoriesEndpointInterface
{
    /**
     * @return array<string, mixed>
     */
    public function fetch(): array;
}
