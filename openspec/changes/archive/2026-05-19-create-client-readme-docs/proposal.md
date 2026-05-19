## Why

The Palermo Bakery project has accumulated 13 active OpenSpec capabilities, 50+ distinct features (gallery, favourites, sharing, performance fixes, forms, colour-swap), and a non-trivial set of operational caveats (WP Engine OPcache, Simple Custom CSS and JS plugin injection, image-naming conventions). The previous client-facing docs (`readme-client.md` and `client-docs/delivery.md`) were deleted during the May 17 refactor and were never replaced. James (the client) has no current single source of truth that explains, in his own terms, what was delivered, how to maintain it, when to call us back, and which conventions must be respected when uploading new cakes — and any technical collaborator who joins later has no narrative companion to the OpenSpec specs. We need a single `docs/client-readme.md` that serves both audiences: a non-technical owner who needs to operate the site, and a technical reader who needs the wiring diagram.

## What Changes

- Add a new authoritative client-facing document at `docs/client-readme.md` that consolidates every feature delivered across the three delivery phases (Minor site updates, Favourites & Sharing, Auto-change Cake Images).
- The document SHALL NOT include any pricing, payment amounts, invoice references, or commercial figures. Pricing is a private matter between developer and client and lives outside the repository.
- The document SHALL follow the three-section structure from the `delivery-template.md` (Human-Language README, Decision Log, Maintenance Guide) and SHALL apply that structure to **every** feature group, not the project as a whole — producing an extensive document, not a brief one.
- The document SHALL maintain a clear, visible distinction between **non-technical** content (for James and bakery operators) and **technical deep-dive** content (for any developer who picks up the project later). Distinction is enforced through explicit "👤 For the bakery team" / "🛠 For developers" subsections on each topic, never mixed prose.
- The document SHALL include the load-bearing constants the project depends on (CF7 form 1874, page id 12 / slug `favorite-cakes`, page id 1122, slug `cake-gallery`) as a clearly-labelled reference table.
- The document SHALL list every required WordPress plugin and theme dependency.
- The document SHALL document the cake-image upload conventions (plain white background, identical filenames with `white`/`ivory` swap, same file extension) and explicitly mark the four legacy filename exceptions as **frozen** — not to be expanded.
- The document SHALL include a "Future enhancements / not yet delivered" section listing the deferred items (email-channel sharing, consultant address book, ESP integration, i18n, separator system, third colour option, optional background-removal local-script extra) so neither side has to re-derive scope.
- The document SHALL include the operational guide for editing the live site safely on WP Engine (WP File Manager plugin path, OPcache invalidation, private/incognito testing, WPvivid backup before major updates).
- The document SHALL NOT contain inspiration attributions (no "Cakestore Mommy" reference), separator instructions, watermark instructions, or marketing-list instructions.
- This change creates `docs/` as a new top-level directory (the repo currently has no `docs/`).

## Capabilities

### New Capabilities

- `client-readme-docs`: Defines the structure, audience-split format, mandatory sections, content rules, and exclusion rules for the master client delivery document at `docs/client-readme.md`. Specifies that every feature group is described through Human-Language README + Decision Log + Maintenance Guide lenses, with explicit non-technical vs technical content separation.

### Modified Capabilities

<!-- None. This is a documentation deliverable; no existing spec's behaviour changes. -->

## Impact

- **New file**: `docs/client-readme.md` (extensive, expected ≥800 lines covering all 13 capabilities + the 50+ feature catalogue).
- **New directory**: `docs/` (top-level, sibling to `src/`, `openspec/`).
- **No code changes.** No JS, CSS, PHP, or `functions.php` is modified.
- **No spec content changes.** The 13 existing capabilities under `openspec/specs/` remain untouched; the new doc references them as the technical source of truth.
- **Affected stakeholders**:
  - James (client) — gains a maintainable operations manual.
  - Future developers — gain a narrative entry point that maps human-readable feature names onto the OpenSpec capabilities.
  - Bakery operators uploading new cakes — gain a single place documenting the file-naming convention.
- **Out of scope** (deferred to future scope, listed only as anticipated future enhancements inside the doc):
  - Email-channel sharing / consultant inbox
  - ESP / marketing-list automation
  - i18n
  - Re-introducing the separator system
  - Third colour option in the wedding-cake switcher
  - Optional background-removal local script + tutorial (offered as extra, not bundled)
