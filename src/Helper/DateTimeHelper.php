<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Helper;

final class DateTimeHelper
{
    public static function parseOrNull(mixed $value): ?\DateTimeInterface
    {
        if ($value === null || $value === '') {
            return null;
        }

        $string = ArrayHelper::asString($value);
        if ($string === '') {
            return null;
        }

        $parsed = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $string);
        if ($parsed instanceof \DateTimeInterface) {
            return $parsed;
        }

        try {
            return new \DateTimeImmutable($string);
        } catch (\Exception) {
            return null;
        }
    }
}
