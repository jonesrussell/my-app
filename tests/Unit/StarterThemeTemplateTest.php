<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Waaseyaa\SSR\SsrServiceProvider;

final class StarterThemeTemplateTest extends TestCase
{
    public function testHomeTemplateRendersStarterLayout(): void
    {
        $html = $this->render('home.html.twig', [
            'site_name' => 'Waaseyaa',
            'demo' => [
                'hero' => [
                    'title' => 'A clean public site for structured content.',
                    'subtitle' => 'Starter test',
                ],
                'features' => [[
                    'title' => 'Twig SSR',
                    'body' => 'Server rendered pages.',
                    'href' => '/',
                    'cta' => 'View home',
                ]],
                'latest' => [[
                    'title' => 'Example story',
                    'type' => 'Story',
                    'href' => '/story/1',
                ]],
                'cta' => [
                    'title' => 'Ready to make it yours?',
                    'body' => 'Replace this fallback page.',
                ],
            ],
        ]);

        self::assertStringContainsString('Skip to content', $html);
        self::assertStringContainsString('/themes/default/theme.css', $html);
        self::assertStringContainsString('/theme/brand.css', $html);
        self::assertStringContainsString('<header class="site-header">', $html);
        self::assertStringContainsString('<main id="main-content"', $html);
        self::assertStringContainsString('<footer class="site-footer">', $html);
        self::assertStringContainsString('A clean public site for structured content.', $html);
        self::assertStringContainsString('Search content', $html);
        self::assertStringContainsString('Latest content', $html);
        self::assertStringContainsString('Ready to make it yours?', $html);
    }

    public function testPageEntityAndNotFoundTemplatesRender(): void
    {
        $page = $this->render('page.html.twig', [
            'site_name' => 'Waaseyaa',
            'title' => 'About',
            'path' => '/about',
        ]);
        $entity = $this->render('entity.html.twig', [
            'site_name' => 'Waaseyaa',
            'entity_type' => 'story',
            'entity' => (object) [
                'id' => 'story-1',
                'label' => 'Six Beings and the Five Totems',
            ],
            'fields' => [
                'body' => [
                    'raw' => 'Story body',
                    'formatted' => 'Story body',
                    'type' => 'text',
                ],
                'source_url' => [
                    'raw' => 'https://example.test/story',
                    'formatted' => 'https://example.test/story',
                    'type' => 'string',
                ],
            ],
        ]);
        $notFound = $this->render('404.html.twig', [
            'site_name' => 'Waaseyaa',
        ]);

        self::assertStringContainsString('Path: /about', $page);
        self::assertStringContainsString('Six Beings and the Five Totems', $entity);
        self::assertStringContainsString('<dt>Body</dt>', $entity);
        self::assertStringContainsString('https://example.test/story', $entity);
        self::assertStringNotContainsString('<li>text</li>', $entity);
        self::assertStringNotContainsString('<li>string</li>', $entity);
        self::assertStringContainsString('That page is not here.', $notFound);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function render(string $template, array $context): string
    {
        $projectRoot = dirname(__DIR__, 2);
        $twig = SsrServiceProvider::createTwigEnvironment($projectRoot);

        return $twig->render($template, $context);
    }
}
