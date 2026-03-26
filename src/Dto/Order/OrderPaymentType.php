<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order;

/**
 * Known order payment types from InPost API payloads.
 */
enum OrderPaymentType: string
{
    case CARD = 'CARD';
    case CARD_TOKEN = 'CARD_TOKEN';
    case GOOGLE_PAY = 'GOOGLE_PAY';
    case APPLE_PAY = 'APPLE_PAY';
    case BLIK_CODE = 'BLIK_CODE';
    case BLIK_TOKEN = 'BLIK_TOKEN';
    case PAY_BY_LINK = 'PAY_BY_LINK';
    case SHOPPING_LIMIT = 'SHOPPING_LIMIT';
    case DEFERRED_PAYMENT = 'DEFERRED_PAYMENT';
    case CASH_ON_DELIVERY = 'CASH_ON_DELIVERY';
    case UNKNOWN = 'UNKNOWN';

    public static function fromRaw(mixed $raw): self|string|null
    {
        if ($raw === null) {
            return null;
        }

        if (is_string($raw)) {
            $value = $raw;
        } elseif (is_scalar($raw)) {
            $value = (string) $raw;
        } else {
            return null;
        }

        if ($value === '') {
            return null;
        }

        return self::tryFrom($value) ?? $value;
    }
}
