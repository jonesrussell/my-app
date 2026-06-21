<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

final class ThemeBrandController
{
    /** @var array<string, string> */
    private const DEFAULT_BRAND = [
        'logo' => '/themes/default/assets/logo.svg',
        'primary' => '#1f6f5b',
        'accent' => '#d97a3a',
        'font' => 'Inter, system-ui, sans-serif',
        'heroImage' => '',
    ];

    public function css(): Response
    {
        $css = self::cssFromBrand(self::loadBrand(dirname(__DIR__, 2)));

        return new Response($css, 200, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=60',
        ]);
    }

    /**
     * @return array<string, string>
     */
    public static function loadBrand(string $projectRoot): array
    {
        $brand = self::DEFAULT_BRAND;
        $path = rtrim($projectRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'theme.brand.json';
        if (!is_file($path)) {
            return $brand;
        }

        try {
            $decoded = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return $brand;
        }

        if (!is_array($decoded)) {
            return $brand;
        }

        foreach (array_keys(self::DEFAULT_BRAND) as $key) {
            if (isset($decoded[$key]) && is_string($decoded[$key])) {
                $brand[$key] = $decoded[$key];
            }
        }

        return $brand;
    }

    /**
     * @param array<string, string> $brand
     */
    public static function cssFromBrand(array $brand): string
    {
        $logo = self::sanitizeAssetPath($brand['logo'] ?? self::DEFAULT_BRAND['logo']) ?? self::DEFAULT_BRAND['logo'];
        $primary = self::sanitizeColor($brand['primary'] ?? self::DEFAULT_BRAND['primary']) ?? self::DEFAULT_BRAND['primary'];
        $accent = self::sanitizeColor($brand['accent'] ?? self::DEFAULT_BRAND['accent']) ?? self::DEFAULT_BRAND['accent'];
        $font = self::sanitizeFontStack($brand['font'] ?? self::DEFAULT_BRAND['font']) ?? self::DEFAULT_BRAND['font'];
        $heroImage = self::sanitizeAssetPath($brand['heroImage'] ?? '');

        $lines = [
            ':root {',
            '  --w-color-primary: ' . $primary . ';',
            '  --w-color-accent: ' . $accent . ';',
            '  --w-font-sans: ' . $font . ';',
            '  --w-logo-url: url("' . $logo . '");',
        ];

        if ($heroImage !== null && $heroImage !== '') {
            $lines[] = '  --w-hero-image: url("' . $heroImage . '");';
        }

        $lines[] = '}';

        return implode("\n", $lines) . "\n";
    }

    private static function sanitizeColor(string $color): ?string
    {
        $color = trim($color);
        if (preg_match('/^#[0-9a-fA-F]{6}$/', $color) !== 1) {
            return null;
        }

        return strtolower($color);
    }

    private static function sanitizeAssetPath(string $path): ?string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        if (!str_starts_with($path, '/')) {
            return null;
        }

        if (preg_match('/[\x00-\x1f"\'\\\\<>]/', $path) === 1) {
            return null;
        }

        return $path;
    }

    private static function sanitizeFontStack(string $font): ?string
    {
        $font = trim($font);
        if ($font === '' || strlen($font) > 160) {
            return null;
        }

        if (preg_match('/^[A-Za-z0-9 ,"\'.-]+$/', $font) !== 1) {
            return null;
        }

        return $font;
    }
}
