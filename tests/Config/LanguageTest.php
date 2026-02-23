<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Config;

use malpka32\InPostBuySdk\Config\Language;
use PHPUnit\Framework\TestCase;

final class LanguageTest extends TestCase
{
    public function testPolishHasCorrectValue(): void
    {
        $this->assertSame('pl', Language::Polish->value);
    }

    public function testEnglishHasCorrectValue(): void
    {
        $this->assertSame('en', Language::English->value);
    }

    public function testAllCases(): void
    {
        $cases = Language::cases();
        $this->assertCount(2, $cases);
        $this->assertContains(Language::Polish, $cases);
        $this->assertContains(Language::English, $cases);
    }
}
