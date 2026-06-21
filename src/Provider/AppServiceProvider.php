<?php

declare(strict_types=1);

namespace App\Provider;

use App\Controller\HomeController;
use App\Controller\ThemeBrandController;
use Waaseyaa\Access\AccountInterface;
use Waaseyaa\Foundation\ServiceProvider\ServiceProvider;
use Waaseyaa\Mcp\Auth\BearerTokenAuth;
use Waaseyaa\Mcp\Auth\WriteTierAuthInterface;
use Waaseyaa\Routing\RouteBuilder;
use Waaseyaa\Routing\WaaseyaaRouter;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * The opaque bearer token a guiding agent presents on POST /mcp/write.
     * In a real deployment this maps to a real, capability-holding user account
     * minted per privileged agent; for this showcase it maps to a single
     * synthetic presenter identity (below).
     */
    public const string WAYFINDING_WRITE_TOKEN = 'wayfinding-demo-token';

    public function register(): void
    {
        // --- Wayfinding authenticated MCP write tier (framework alpha.234 P0-1) ---
        // Bind the app's WriteTierAuthInterface so POST /mcp/write authenticates a
        // bearer token mapped to an account holding the 'present guided content'
        // capability. The framework's McpServiceProvider deliberately binds NO
        // package default for this interface and resolves it through the
        // cross-provider kernel-services bus, so this app binding actually wins
        // (the alpha.233 shadowing bug is fixed). The write tier then scopes
        // itself to the 'present guided content' capability, exposing exactly the
        // four wayfinding write tools (record/re-record/get trail, emit beacon).
        $presenter = new class implements AccountInterface {
            public function id(): int|string
            {
                return 4242;
            }

            public function hasPermission(string $permission): bool
            {
                return $permission === 'present guided content';
            }

            /** @return string[] */
            public function getRoles(): array
            {
                return ['wayfinding_presenter'];
            }

            public function isAuthenticated(): bool
            {
                return true;
            }
        };

        $this->singleton(
            WriteTierAuthInterface::class,
            fn (): WriteTierAuthInterface => new BearerTokenAuth([
                self::WAYFINDING_WRITE_TOKEN => $presenter,
            ]),
        );
    }

    public function routes(WaaseyaaRouter $router, ?\Waaseyaa\Entity\EntityTypeManager $entityTypeManager = null): void
    {
        $homeController = new HomeController();
        $themeBrandController = new ThemeBrandController();

        $router->addRoute(
            'home',
            RouteBuilder::create('/')
                ->controller(fn () => $homeController->index())
                ->allowAll()
                ->methods('GET')
                ->build(),
        );

        $router->addRoute(
            'theme.brand_css',
            RouteBuilder::create('/theme/brand.css')
                ->controller(fn () => $themeBrandController->css())
                ->allowAll()
                ->methods('GET')
                ->priority(20)
                ->build(),
        );
    }
}
