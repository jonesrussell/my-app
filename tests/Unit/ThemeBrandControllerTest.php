<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Controller\ThemeBrandController;
use PHPUnit\Framework\TestCase;

final class ThemeBrandControllerTest extends TestCase
{
    public function testBrandCssEmitsSanitizedVariables(): void
    {
        $css = ThemeBrandController::cssFromBrand([
            'logo' => '/themes/default/assets/custom.svg',
            'primary' => '#ABCDEF',
            'accent' => '#123456',
            'font' => 'Atkinson Hyperlegible, system-ui, sans-serif',
            'heroImage' => '/themes/default/assets/hero.jpg',
        ]);

        self::assertStringContainsString('--w-color-primary: #abcdef;', $css);
        self::assertStringContainsString('--w-color-accent: #123456;', $css);
        self::assertStringContainsString('--w-font-sans: Atkinson Hyperlegible, system-ui, sans-serif;', $css);
        self::assertStringContainsString('--w-logo-url: url("/themes/default/assets/custom.svg");', $css);
        self::assertStringContainsString('--w-hero-image: url("/themes/default/assets/hero.jpg");', $css);
    }

    public function testBrandCssFallsBackWhenValuesAreInvalid(): void
    {
        $css = ThemeBrandController::cssFromBrand([
            'logo' => 'https://example.test/logo.svg',
            'primary' => 'expression(alert(1))',
            'accent' => '#123',
            'font' => 'Inter; body { display:none }',
            'heroImage' => '/bad"image.jpg',
        ]);

        self::assertStringContainsString('--w-color-primary: #1f6f5b;', $css);
        self::assertStringContainsString('--w-color-accent: #d97a3a;', $css);
        self::assertStringContainsString('--w-font-sans: Inter, system-ui, sans-serif;', $css);
        self::assertStringContainsString('--w-logo-url: url("/themes/default/assets/logo.svg");', $css);
        self::assertStringNotContainsString('--w-hero-image:', $css);
    }

    public function testMissingBrandFileReturnsDefaults(): void
    {
        $brand = ThemeBrandController::loadBrand(sys_get_temp_dir() . '/waaseyaa-missing-brand-' . uniqid('', true));

        self::assertSame('/themes/default/assets/logo.svg', $brand['logo']);
        self::assertSame('#1f6f5b', $brand['primary']);
        self::assertSame('#d97a3a', $brand['accent']);
    }
}
