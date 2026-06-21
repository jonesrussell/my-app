# Minimal Starter Theme, App First

## Summary

Build the starter theme in `my-app` first, prove it against real SSR and content routes, then backport only the stable theme mechanics into Waaseyaa framework. The next cut release should promise that a fresh Waaseyaa app renders a clean, branded, accessible public site out of the box using Twig SSR.

## Decisions

- Prototype in `my-app` before editing framework package defaults.
- Use Twig SSR templates, not Blade.
- Use plain CSS and static public assets for the next cut; no Tailwind or build step.
- Keep branding to one JSON file, `themes/default/theme.brand.json`, bridged into CSS by `GET /theme/brand.css`.
- Treat this as a public-site starter theme only; admin SPA theming remains out of scope.
- Keep the first-install homepage safe to replace: it renders seeded fallback content now, and the framework backport should prefer an editor-owned `home` entity when one exists.

## Implemented App Shape

- App template overrides:
  - `templates/layouts/base.html.twig`
  - `templates/home.html.twig`
  - `templates/page.html.twig`
  - `templates/entity.html.twig`
  - `templates/404.html.twig`
- Reusable partials:
  - `templates/partials/_header.html.twig`
  - `templates/partials/_footer.html.twig`
  - `templates/partials/_card.html.twig`
  - `templates/partials/_field.html.twig`
- Public theme assets:
  - `public/themes/default/theme.css`
  - `public/themes/default/assets/logo.svg`
- Branding contract:
  - `themes/default/theme.brand.json`
  - `/theme/brand.css`
- Homepage fallback blocks:
  - Hero.
  - Search panel.
  - Feature grid.
  - Latest content feed.
  - Replacement call to action.

## Homepage Behavior

The app prototype ships a polished fallback homepage using seeded demo content from `HomeController`. This keeps a fresh install visually complete before any content exists.

The framework backport should add content-first resolution:

1. Check for a site- or tenant-scoped `home` content entity.
2. If present, render that entity through the normal SSR entity template path.
3. If absent, render `home.html.twig` with seeded demo content.

The desired home content model for the framework version is:

- Hero block: title, subtitle, CTA, optional background image.
- Feature grid: three or four repeatable cards.
- Latest content feed: recent stories, posts, or pages.
- Search bar: public content search backed by API routes.
- Call to action: admin, signup, or first content action.
- Footer and site metadata.
- Optional editorial blocks: promo banner, announcement, featured story.

Admin page-builder support is not part of this app prototype, but the fallback layout is shaped so those blocks can later become editor-owned fields or paragraphs.

## Accessibility And Layout Requirements

- Include a skip link targeting the main landmark.
- Use semantic header, main, footer, nav, article, section, dl, dt, and dd elements where appropriate.
- Provide visible keyboard focus states.
- Keep readable line lengths and responsive layout constraints.
- Keep cards at an 8px radius or less.
- Avoid a frontend build step so first install remains reliable.

## Brand JSON Contract

Stable keys for this prototype:

```json
{
  "logo": "/themes/default/assets/logo.svg",
  "primary": "#1f6f5b",
  "accent": "#d97a3a",
  "font": "Inter, system-ui, sans-serif",
  "heroImage": ""
}
```

Stable CSS variables for backport consideration:

- `--w-color-primary`
- `--w-color-accent`
- `--w-font-sans`
- `--w-logo-url`
- `--w-hero-image`

The app bridge sanitizes colors, asset paths, and font stacks before emitting CSS. Missing or invalid brand files fall back to safe defaults.

## Backport Path

After `my-app` is proven:

- Package the templates and assets as the framework default starter theme.
- Add or extend a scaffold command so new apps receive the theme files and brand JSON.
- Consider moving `/theme/brand.css` support into SSR/theming after the app route proves the JSON contract.
- Backport only stable contracts:
  - Theme template names.
  - Brand JSON keys.
  - CSS variable names.
  - Asset path conventions.

## Deferred Phase Two

- Tailwind preset.
- Critical CSS extraction.
- Streaming shell.
- On-demand homepage revalidation endpoint.
- Home content entity and route resolution.
- Partial hydration.
- CDN adapters.
- Admin brand panel.
- Admin page-builder entry for homepage blocks.
- Marketplace export.
- Theme package publishing workflow.
- Visual regression and axe accessibility checks in CI.

## Test Plan

- Static/template checks:
  - Render `home.html.twig`, `page.html.twig`, `entity.html.twig`, and `404.html.twig` with representative context.
  - Assert the base layout includes skip link, stylesheet links, header, main, footer, and title blocks.
- Brand CSS checks:
  - Valid `theme.brand.json` emits expected CSS variables.
  - Missing or invalid brand file falls back to safe defaults.
  - Invalid colors/fonts are ignored or escaped safely.
- Browser smoke:
  - Start the app and open `/`, a Story entity page, and a missing route.
  - Verify pages are styled, responsive, readable, and have no obvious layout overlap.
- Homepage checks:
  - Fallback homepage includes hero, search, feature cards, latest feed, and replacement CTA.
  - Backport route resolution prefers a `home` entity over fallback content.
- Regression checks:
  - Existing public SSR entity rendering still works.
  - Admin SPA routes remain unaffected.
  - JSON:API and MCP routes remain unaffected.

## Metrics To Track After Backport

- First Contentful Paint and Largest Contentful Paint for `/`.
- Flash-of-unstyled-content incidents on throttled network tests.
- Time to brand: logo plus primary color change.
- Percentage of installs that keep the default theme after seven days.

## Release Note Draft

Waaseyaa now ships with a minimal starter theme path for public Twig SSR pages: a clean base layout, public entity page template, safe one-file brand configuration, and static CSS/assets that work without a frontend build step. The app-level prototype is intentionally small so the framework backport can keep only the contracts that prove stable.
