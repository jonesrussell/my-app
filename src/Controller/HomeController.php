<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Waaseyaa\SSR\SsrServiceProvider;

final class HomeController
{
    public function index(): Response
    {
        $projectRoot = dirname(__DIR__, 2);
        $twig = SsrServiceProvider::getTwigEnvironment()
            ?? SsrServiceProvider::createTwigEnvironment($projectRoot);

        $html = $twig->render('home.html.twig', [
            'title' => 'Waaseyaa',
            'site_name' => 'Waaseyaa',
            'brand' => ThemeBrandController::loadBrand($projectRoot),
            'demo' => self::demoHomeContent(),
            'primary_nav' => [
                ['label' => 'Stories', 'href' => '/story/1'],
                ['label' => 'Admin', 'href' => '/admin/'],
                ['label' => 'API', 'href' => '/api/story'],
            ],
        ]);

        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /**
     * @return array<string, mixed>
     */
    private static function demoHomeContent(): array
    {
        return [
            'hero' => [
                'eyebrow' => 'Fresh Waaseyaa starter',
                'title' => 'A clean public site for structured content.',
                'subtitle' => 'Publish once, then serve people, APIs, and agents from the same content model.',
                'primaryCta' => ['label' => 'Read a story', 'href' => '/story/1'],
                'secondaryCta' => ['label' => 'Open admin', 'href' => '/admin/'],
            ],
            'features' => [
                [
                    'title' => 'Twig SSR',
                    'body' => 'Public pages render immediately through server-side Twig, with no frontend build step required.',
                    'href' => '/',
                    'cta' => 'View home',
                ],
                [
                    'title' => 'Structured content',
                    'body' => 'Entity pages inherit the public layout and field rendering without custom route wiring.',
                    'href' => '/story/1',
                    'cta' => 'Read a story',
                ],
                [
                    'title' => 'API first',
                    'body' => 'The same content is ready for public pages, JSON:API clients, GraphQL, and agent reads.',
                    'href' => '/api/story',
                    'cta' => 'Inspect API',
                ],
                [
                    'title' => 'One-file brand',
                    'body' => 'Colors, logo, and fonts come from a small JSON file exposed as safe CSS variables.',
                    'href' => '/theme/brand.css',
                    'cta' => 'Inspect brand CSS',
                ],
            ],
            'latest' => [
                [
                    'title' => 'The Six Beings and the Five Totems',
                    'type' => 'Story',
                    'href' => '/story/1',
                ],
                [
                    'title' => 'Draft content stays manageable in admin',
                    'type' => 'Workflow',
                    'href' => '/admin/story',
                ],
                [
                    'title' => 'Brand tokens are served as CSS',
                    'type' => 'Theme',
                    'href' => '/theme/brand.css',
                ],
            ],
            'cta' => [
                'title' => 'Ready to make it yours?',
                'body' => 'Replace this fallback page with a home content entity when the site needs editor-owned blocks.',
                'primaryCta' => ['label' => 'Create content', 'href' => '/admin/'],
                'secondaryCta' => ['label' => 'Inspect brand', 'href' => '/theme/brand.css'],
            ],
        ];
    }
}
