<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Client;

use malpka32\InPostBuySdk\Dto\CategoryDto;
use malpka32\InPostBuySdk\Dto\OfferDto;
use malpka32\InPostBuySdk\Dto\OrderDto;
use malpka32\InPostBuySdk\Dto\OrderStatusDto;

/**
 * Kontrakt klienta API InPost Buy (inpsa).
 * Dokumentacja: https://inpsa-api-portal.inpost-group.com/
 */
interface InPostBuyClientInterface
{
    /**
     * Pobiera listę kategorii (drzewo lub lista).
     *
     * @return CategoryDto[]
     */
    public function getCategories(): array;

    /**
     * Tworzy lub aktualizuje kategorię po stronie InPost (API inpsa nie wspiera – rzuca wyjątek).
     */
    public function putCategory(CategoryDto $dto): string;

    /**
     * Wysyła ofertę (produkt) do InPost. Zwraca ID oferty.
     */
    public function putOffer(OfferDto $dto): string;

    /**
     * Pobiera listę zamówień (z opcjonalnym filtrem statusu).
     *
     * @return OrderDto[]
     */
    public function getOrders(?\DateTimeInterface $since = null, ?string $status = null): array;

    /**
     * Pobiera pojedyncze zamówienie po ID InPost.
     */
    public function getOrder(string $inpostOrderId): ?OrderDto;

    /**
     * Aktualizuje status zamówienia po stronie InPost (accept / refuse).
     */
    public function updateOrderStatus(string $inpostOrderId, OrderStatusDto $status): void;
}
