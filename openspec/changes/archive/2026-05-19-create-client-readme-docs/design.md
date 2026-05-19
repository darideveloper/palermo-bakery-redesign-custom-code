## Context

The Palermo Bakery codebase ships ~50 distinct features grouped into 13 OpenSpec capabilities, delivered iteratively across 2026. The previous client-facing docs (`readme-client.md`, `client-docs/delivery.md`) were authored iteratively as the project evolved and were deleted during the May 17 refactor. Today there is no document that explains the project to its primary audience (James, the client) or to a future technical collaborator. OpenSpec captures the *technical* requirements but is unreadable to a non-developer. This design defines a single, audience-split `docs/client-readme.md` that fills both roles.

**Stakeholders:**
- **James (`jweber1212`)** — client, project owner, will use the doc to operate, troubleshoot, and decide when to commission new work.
- **Bakery operators** — non-technical staff who upload cakes, edit CF7 forms, and add categories.
- **Future developers** — including the original author after a long gap, or any collaborator who inherits the project.
- **The `delivery-template.md`** authored by daridev — its three-section structure (Human-Language README, Decision Log, Maintenance Guide) is the recommended pattern at the agency level and must be honoured.

**Constraints:**
- Documentation only. No code, no specs, no behavioural changes.
- Must be self-contained — the reader should not need to open OpenSpec to operate the site.
- Must respect the user-confirmed exclusions: no email-sharing, no separators, no Cakestore Mommy reference, no marketing-ESP details, no i18n, no browser-floor discussion, no expansion of the colour-swap exception map.
- Must be loaded with operational facts that exist *only* in commit history or in the Fiverr transcript and are at risk of being lost (CF7 form IDs, WP Engine OPcache caveat, image-naming convention, plugin list).

## Goals / Non-Goals

**Goals:**
- Produce a single Markdown file (`docs/client-readme.md`) that is the master delivery record.
- Provide a complete feature catalogue, organised by feature group (Gallery, Favourites, Sharing, Lightbox, Forms, Order-Wedding-Cake, Auth, Branding, Performance, Plugins/Dependencies).
- For every feature group apply the template's three lenses (what it does in plain language → why we built it that way → how to maintain / when to call us back).
- Maintain a hard visual separation between non-technical and technical content so a non-coder can read the doc without being overwhelmed and a developer can find the wiring details without scrolling through marketing prose.
- Document load-bearing constants (CF7 form 1874, page id 12, page id 1122, shop slug `cake-gallery`, favourites slug `favorite-cakes`) in one reference table.
- Document plugin/theme dependencies that the project requires to function.
- Document image-upload naming conventions and freeze the legacy exceptions.
- Document operational gotchas (WP Engine OPcache, WP File Manager plugin, incognito QA, WPvivid backups).
- List future-version features as "anticipated future work, scope for a future engagement" — both as memory for daridev and as expectation-setting for James.
- Exclude any commercial figures — no prices, no payment amounts, no invoice references — from every artifact and from the resulting document. Commercial details are handled privately between developer and client and never enter the repository.

**Non-Goals:**
- Will not duplicate the OpenSpec requirement text. The doc references capabilities by name; the binding contract remains the spec files.
- Will not document removed features (separators, daridev mail-API, watermark, IE polyfills) — they are not part of the current delivery.
- Will not include design-inspiration attribution.
- Will not document deployment automation (the team deploys manually via WP File Manager; no CI is in scope).
- Will not include marketing copy or sales pitches beyond what's necessary to explain a feature's purpose.
- Will not include screenshots in this change — the doc is text-first; if visuals are wanted later they can be appended without restructuring.

## Decisions

### Decision 1: Single file at `docs/client-readme.md`, not a multi-file knowledge base.

**Choice:** One Markdown file, with a table of contents and stable anchor links.

**Why:**
- The client received earlier docs as single files (`readme-client.md`, `client-docs/delivery.md`); a single file is what they expect.
- A multi-file `docs/` site (Hugo, Docusaurus, MkDocs) introduces tooling and deployment overhead this project explicitly forbids ("not runnable locally" per `openspec/project.md`).
- A single file can be opened in a Fiverr DM, pasted into Notion, or sent as a PDF without conversion.

**Alternatives considered:**
- `docs/index.md` + per-feature subpages — rejected for tooling overhead.
- Inline as `README.md` at repo root — rejected; root README is for developers cloning the repo, not for the client. The two audiences split cleanly along that line.

### Decision 2: Audience-split via explicit "👤 For the bakery team" / "🛠 For developers" subsections.

**Choice:** Inside every feature section, the non-technical explanation comes first under a "👤 For the bakery team" subheading, followed by a separate "🛠 For developers" subheading with the technical wiring details. The two are never interleaved into the same paragraph.

**Why:**
- The client transcript reveals James asks operational questions ("how do I add a cake?") and skips technical ones; mixing the two confuses both audiences.
- Emoji prefixes make the split scannable without imposing a heavyweight tab/component system.
- Each developer block can reference the OpenSpec capability by name, giving a clean bridge into the canonical technical spec.

**Alternatives considered:**
- Two separate documents (one client, one dev) — rejected because every feature exists for both audiences and the duplication invites drift.
- HTML `<details>` collapsibles — rejected; renders differently across GitHub / Notion / PDF and isn't grep-friendly.

### Decision 3: Three-lens application of the delivery template per feature group, not per project.

**Choice:** Instead of applying "Human-Language README + Decision Log + Maintenance Guide" once to the whole project, apply it once to **each feature group**. So each feature group has its own mini "what it does / why we built it this way / how to maintain it" mini-document.

**Why:**
- The template was designed for one script delivering one outcome. This project delivers ~10 feature groups, each with its own rationale and maintenance story.
- Reading "the project as a whole" loses the specificity the client needs ("what do I do if the Pindots cake colour swap breaks?" — that answer only exists at the feature-group level).
- It maps cleanly onto the existing OpenSpec capability structure: each feature group corresponds to one or more capabilities.

**Alternatives considered:**
- Single project-level three-lens summary + flat feature catalogue — rejected, loses the per-feature maintenance guidance.
- Three-lens applied only to client-visible features, plain catalogue for developer-only features — rejected; performance fixes are developer-facing but their *symptom* (iOS freeze) is client-facing, so they deserve a maintenance guide too.

### Decision 4: Load-bearing constants live in a single "Quick-reference table" near the top of the doc.

**Choice:** A single table titled something like "Important IDs and slugs (don't change without consulting your developer)" that lists CF7 form id 1874, WordPress page ids (12, 1122), key URL slugs (`cake-gallery`, `favorite-cakes`, `order-wedding-cake`), required user-meta key (`my_cake_favorites`), AJAX action names (`save_user_favorites`, `get_user_favorites`, `render_favorite_products`), nonce action name (`cake_fav_nonce`), and CSS classes that operators might accidentally remove (`.wpcf7-form`, `.product-inner`, etc.).

**Why:**
- These are the silent failure points. Changing CF7 form 1874's ID renumbers the form and silently breaks the popup auto-close. Renaming the favourites page slug breaks the prettyPhoto force-enqueue.
- Putting them in one obvious table makes the failure surface visible.

**Alternatives considered:**
- Sprinkling each constant in its feature section — rejected; James won't read every section, but he might glance at a single table.

### Decision 5: Documented future-features list, even though they're explicitly out of scope today.

**Choice:** A "Future enhancements (not yet delivered)" section lists the deferred items with the exact scope already discussed: email-channel sharing, consultant address book, ESP integration, i18n, separator system re-enable, third colour option, background-removal local script + tutorial.

**Why:**
- Both daridev and James have referenced these in the Fiverr conversation. Forgetting them invites a re-derivation of scope later.
- It pre-emptively answers the "could we add X?" question and reframes it as "here's the agreed deferred list; pick from this menu when you want a follow-up engagement".
- The user explicitly asked to "take note of all the mentioned features" but "don't do anything with them" — a documentation note is the lightest-touch way to honour that.

**Alternatives considered:**
- Stash future-features in a private daridev-only doc — rejected; the user wanted them documented for *future documentation reuse* and a single source is easier.
- Omit them entirely — rejected; would lose context that took multiple Fiverr threads to establish.

### Decision 6: Frozen-exception policy for the colour-swap filename map.

**Choice:** The doc explicitly states (a) the convention for *new* cakes (identical filenames, only the `white`/`ivory` token differs, same file extension), and (b) the four legacy exceptions (Rustic-Stucco, Pindots, Screen-Shot, permanent-white Exquisite) are **frozen** and will not be expanded — meaning new cakes that don't follow the convention will not be auto-swapped.

**Why:**
- The user explicitly said "freeze the exceptions" in the prior turn.
- Stating the policy in the doc protects daridev: if a future cake fails to swap, James can self-diagnose ("did I follow the naming rule?") rather than calling for a paid fix.

**Alternatives considered:**
- Document the convention silently and let exceptions accumulate as bugs are reported — rejected; that's how the current four exceptions accumulated.

### Decision 7: Plugin and theme dependency manifest as a checklist.

**Choice:** A "Required modules" subsection lists, by name and role: WordPress + WooCommerce, the SNS Vicky theme (header-style4 only), Simple Custom CSS and JS, Contact Form 7, Theme My Login, YITH WooCommerce Wishlist, WPBakery / Visual Composer, WPvivid Backup. Each entry says *why* it's required (with the specific feature(s) that depend on it) so a future support engineer can spot which features break if a plugin is removed.

**Why:**
- The current codebase has implicit dependencies (e.g., we read `.yith-wcwl-add-to-wishlist[data-fragment-ref]` — if YITH is deactivated, every gallery heart breaks silently).
- A checklist also supports staging-site set-up for future developers.

**Alternatives considered:**
- Auto-generate from `wp-content/plugins/` on the live site — rejected; the code repo can't reach the live site, this is meant to be static text.

### Decision 8: Operational guide explains WP Engine specifics inline, not as a generic WordPress guide.

**Choice:** The "When to call me / when you can self-serve" section explicitly names WP File Manager as the editing surface for `functions.php`, names OPcache as the cache that doesn't auto-invalidate, names the `?palermo_reset_opcache=1` snippet if portal access fails, and reminds the operator to use a Private/Incognito window (which bypasses LiteSpeed cache).

**Why:**
- These are the *actual* operational facts that bit the team during delivery — they're documented in archived OpenSpec proposals and commit messages and would otherwise be lost.
- Generic "edit functions.php via the theme editor" advice would actively break the site on this install (Theme File Editor writes to a shadow path on this WP Engine deployment).

**Alternatives considered:**
- Generic WordPress maintenance language — rejected, would cause incidents.

## Risks / Trade-offs

- **Risk: Document length intimidates the client.** → Mitigation: lead with a one-paragraph Executive Summary and a Quick-reference table, then a table of contents. James can read the first page in 2 minutes and skip to a section if needed.

- **Risk: Plugin / theme list drifts as WP admin changes.** → Mitigation: the doc states "as of delivery on 2026-05-17" and instructs the reader to confirm via WP admin before troubleshooting. Spec scenarios verify the list is present and dated, not that it stays in sync.

- **Risk: Documentation duplicates OpenSpec and drifts.** → Mitigation: the doc *references* OpenSpec capability names rather than copying requirement text. The single source of truth for behaviour remains `openspec/specs/`.

- **Risk: Future-features list becomes a binding promise.** → Mitigation: every item in that section is explicitly labelled "not yet delivered; requires a separate scope". The doc opens that section with that disclaimer. No prices or estimates are quoted next to any future item.

- **Risk: The audience split (👤 / 🛠 subsections) is ignored by readers who skim.** → Mitigation: emoji prefix is consistent throughout, plus a "How to read this document" note up front.

- **Trade-off: Single-file vs multi-file.** Single-file is harder to navigate but more portable. Accepted, mitigated by ToC + anchors.

- **Trade-off: Markdown vs rich PDF.** Markdown is the source; if a polished PDF is needed later it can be generated via Pandoc without restructuring.

- **Trade-off: Listing CF7 form id 1874 publicly in the repo.** Acceptable — IDs aren't secrets, and the repo's source-controlled code already references them inline.

## Migration Plan

Not applicable — this is a new file in a new directory. No data, no behaviour, no API. Rollback = delete the file.

## Open Questions

None blocking. Two minor items that can be resolved in the artifact-writing phase without re-prompting:

1. Exact wording of the Executive Summary paragraph — will be drafted to match the tone of the deleted `readme-client.md` (warm, second-person, no jargon) and validated against the user's earlier instructions to omit the Cakestore Mommy reference.
2. Whether to embed a future-features section by feature group or as a single trailing section — will be implemented as a single trailing section so it's easy to update as a future-work menu.
