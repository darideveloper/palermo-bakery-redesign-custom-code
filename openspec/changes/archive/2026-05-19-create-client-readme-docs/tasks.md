## 1. Setup

- [x] 1.1 Create the top-level `docs/` directory at the repository root
- [x] 1.2 Create `docs/client-readme.md` as an empty file with the YAML frontmatter (`created`, `updated`, `tags: [work, client-docs, palermo-bakery]`, `type: area-note`, `status: active`)

## 2. Front-matter and orientation

- [x] 2.1 Write the document title `Palermo Bakery Redesign — Master Delivery Document`
- [x] 2.2 Write the Executive Summary paragraph (2-3 sentences, plain English, no jargon, summarising the three delivery phases by name only — no prices, no payment amounts, no commercial figures of any kind)
- [x] 2.3 Write the "How to read this document" note explaining the 👤 (bakery team) and 🛠 (developers) convention
- [x] 2.4 Write the Table of Contents with anchor links to every major section

## 3. Quick-reference table of load-bearing constants

- [x] 3.1 Build a Markdown table listing every constant from the spec's reference-table requirement (CF7 form 1874; page id 12 / slug `favorite-cakes`; page id 1122; URL slugs `cake-gallery` and `/order-wedding-cake/`; user-meta key `my_cake_favorites`; AJAX actions `save_user_favorites`, `get_user_favorites`, `render_favorite_products`; nonce action `cake_fav_nonce`; URL parameter `shared_favs`)
- [x] 3.2 For every entry, write the failure-mode sentence that explains what breaks if the value changes

## 4. Required modules manifest

- [x] 4.1 Write the "Required modules" section as a checklist with: WordPress, WooCommerce, SNS Vicky theme (note: `header-style4` only), Simple Custom CSS and JS, Contact Form 7, Theme My Login, YITH WooCommerce Wishlist, WPBakery / Visual Composer, WPvivid Backup
- [x] 4.2 For every module, write a one-line "what breaks if this is deactivated" note

## 5. Feature group — Cake Gallery (covers `gallery-optimization`, `gallery-auth-buttons`, `gallery-fav-pill`, plus the unspecced gallery-only redirect)

- [x] 5.1 Write the Human-Language README subsection: 👤 plain-English summary of what the gallery does, 🛠 developer summary referencing `gallery-optimization` and the redirect helper
- [x] 5.2 Write the Decision Log subsection: 👤 why we built it this way (gallery-only mode, prettyPhoto lightbox); 🛠 the iOS-Safari freeze story, sentinels `?t=300` / `?l=1`, rogue script removal, third-party load-blocker dequeue, `_palermo_is_gallery_view()` predicate, chunked card prep
- [x] 5.3 Write the Maintenance Guide subsection: 👤 how to add a cake (use white background, follow WooCommerce normally), what to do if the gallery looks wrong; 🛠 OPcache invalidation steps via WP File Manager, gotchas with single-product redirect

## 6. Feature group — Favourites System (covers `favorites-heart-button`, `favorites-lightbox`, `favorites-full-size-images`, `favorites-shared-save-feedback`, plus the auth-sync logic)

- [x] 6.1 Write the Human-Language README subsection: 👤 explain favouriting cakes, the masonry board at `/favorite-cakes`, the cake-shaped header icon, the persistent login sync; 🛠 reference the listed capabilities and explain the localStorage ↔ server-meta merge
- [x] 6.2 Write the Decision Log subsection: 👤 why we use accounts (so favourites travel between devices); 🛠 hybrid storage, `cakeFavsData` bootstrap, `my_cake_favorites` user-meta, MutationObserver heart injection
- [x] 6.3 Write the Maintenance Guide subsection: 👤 how to remove a cake from the page (via WooCommerce — favourites pointing at it will silently disappear), how the empty-state behaves; 🛠 how to force-enqueue prettyPhoto if page id 12 changes

## 7. Feature group — Sharing System (covers `favorites-share-button`, `lightbox-share-button`)

- [x] 7.1 Write the Human-Language README subsection: 👤 single-cake share and whole-board share, copy-to-clipboard with "Link Copied!" feedback, what a recipient sees ("Cakes Shared With You" section + their independent workspace); 🛠 reference the two share capabilities and the `?shared_favs=` URL contract
- [x] 7.2 Write the Decision Log subsection: 👤 why we built read-only sharing (Client A keeps full control; Client B can save to their own list); 🛠 read-only semantics enforced at the mutation boundary not the URL, share URLs are intentionally public (acknowledged trade-off)
- [x] 7.3 Write the Maintenance Guide subsection: 👤 how to test a share link; 🛠 how to extend the URL contract without breaking existing shared links

## 8. Feature group — Custom Lightbox (covers `lightbox-close-redirection`, `lightbox-fav-button`, plus prettyPhoto UI customisations)

- [x] 8.1 Write the Human-Language README subsection: 👤 what the popup window does, how the favourite/share buttons inside it work, why arrows are mobile-friendly; 🛠 prettyPhoto config (`overlay_gallery: false`, `social_tools: false`, `deeplinking: false`), close → overlay redirection, scoped `prettyPhoto[cake-gallery]` and `prettyPhoto[fav-gallery]` groups
- [x] 8.2 Write the Decision Log subsection: 👤 why we hide the thumbnail strip / expand button; 🛠 the .pp_close → .pp_overlay capture-phase redirect, vertical arrow centering for thumb-reach
- [x] 8.3 Write the Maintenance Guide subsection: 👤 what to do if the lightbox stops opening on a new page; 🛠 force-enqueue pattern for non-shop pages

## 9. Feature group — Authentication (covers `auth-form-layout`)

- [x] 9.1 Write the Human-Language README subsection: 👤 the `/login` and `/register` pages, what email is collected and why (favourites sync), the cross-device promise; 🛠 Theme My Login integration, `.tml` container sizing
- [x] 9.2 Write the Decision Log subsection: 👤 why we use real accounts and not just browser storage (cross-device, lead capture); 🛠 the localStorage merge-on-login behaviour
- [x] 9.3 Write the Maintenance Guide subsection: 👤 how to view the user list in WP admin, manual export workflow as today's substitute for ESP integration; 🛠 user-meta query example

## 10. Feature group — Ask-Me Cupcake Form (covers `form-frontend`)

- [x] 10.1 Write the Human-Language README subsection: 👤 the floating cupcake widget appears on every page, opens a CF7 form, auto-closes after submission; 🛠 form id 1874, DOM relocation pattern, `wpcf7mailsent` listener
- [x] 10.2 Write the Decision Log subsection: 👤 why we use CF7 (so the bakery can edit fields without a developer); 🛠 migration history from the daridev mail API (historical context, not delivery)
- [x] 10.3 Write the Maintenance Guide subsection: 👤 how to edit fields in CF7 admin, how to change the destination email, why the form id must not change; 🛠 what to do if a different CF7 form needs the same treatment

## 11. Feature group — Venue Wedding Cake Form & Footer Form

- [x] 11.1 Write the Human-Language README subsection: 👤 the polished form on `/order-wedding-cake/`, the matching footer newsletter form; 🛠 page id 1122 scoping, dashed file drop-zone, image CAPTCHA radio swatches
- [x] 11.2 Write the Decision Log subsection: 👤 why the form was restyled (matching brand vs. clinical look); 🛠 selector reuse with the footer form via `#my-proprietary-form` legacy id
- [x] 11.3 Write the Maintenance Guide subsection: 👤 how to edit fields via CF7; 🛠 what breaks if page id 1122 changes

## 12. Feature group — Order-Wedding-Cake Colour Switch (no spec; document the behaviour)

- [x] 12.1 Write the Human-Language README subsection: 👤 the White ↔ Ivory toggle at the top of the page, default White, instantaneous image swap; 🛠 exclusive checkbox logic, filename rewriting in `order-wedding-cake-change-cake-color.js`
- [x] 12.2 Write the Decision Log subsection: 👤 why placement at the top of the form (first decision drives every visible image); 🛠 dimension-suffix stripping, why exceptions exist
- [x] 12.3 Write the Maintenance Guide subsection — image-upload conventions (this is the highest-stakes operator instruction in the doc):
  - 👤 Rules for uploading new cakes: identical filename for both variants, only the `white` / `ivory` token differs, same file format (both `.jpg` or both `.png`), plain white photo background
  - 👤 Plain-English statement that the four legacy exceptions are frozen — new cakes that don't follow the convention will not be auto-swapped
  - 👤 List the four frozen exceptions with their pairs so future operators recognise them: Rustic-Stucco, Pindots (`.jpg`/`.png` swap), Screen-Shot, permanent-white Exquisite
  - 🛠 Where the exception map lives in `src/features/order-cake/order-wedding-cake-change-cake-color.js` and why it must not be extended

## 13. Feature group — Branding & Layout

- [x] 13.1 Write the Human-Language README subsection: 👤 the full-height "P" logo, the on-page chrome removed (sidebar, breadcrumbs, top toolbar) to keep the gallery uncluttered; 🛠 `header-style4` lock, hidden-element selectors
- [x] 13.2 Write the Decision Log subsection: 👤 why we chose to hide chrome; 🛠 SNS Vicky theme integration boundary
- [x] 13.3 Write the Maintenance Guide subsection: 👤 what to do if the logo regresses to a small version; 🛠 the only-supported-header note

## 14. Operational maintenance guide (WP Engine specifics)

- [x] 14.1 Write the "How to edit the live site safely" subsection with: WP File Manager plugin path to `/wp-content/themes/snsvicky/functions.php`, why Theme File Editor is unsafe on this install (shadow path), OPcache must be manually invalidated, fallback `?palermo_reset_opcache=1` snippet
- [x] 14.2 Write the "How to test changes" subsection: Private/Incognito browsing to bypass WP Engine + LiteSpeed caches, device matrix (Mobile, Tablet, Laptop, Desktop)
- [x] 14.3 Write the "Before major updates" subsection: run WPvivid backup first, list known fragile areas (custom hearts on cards, prettyPhoto bindings, CF7 form ids)
- [x] 14.4 Write the "When to call us back" subsection: gallery looks broken, lightbox stops opening, favourites stop syncing, share link returns the wrong cake, colour switcher doesn't swap an image

## 15. Future enhancements (not yet delivered) — list with disclaimer

- [x] 15.1 Write the section opening disclaimer: "Nothing in this section is delivered today. Each item requires a separate scope. Use this list as a menu for follow-up work." Do NOT include any pricing, estimates, or commercial figures next to any item
- [x] 15.2 List every deferred item: email-channel sharing for boards (mailto integration, consultant inbox), consultant address book, ESP / marketing-list automation, multi-language support, third colour option on the wedding-cake switcher, an optional background-removal local script + tutorial as an extra deliverable
- [x] 15.3 For each item write a one-sentence summary of the previously-discussed scope so the next engagement starts from a shared understanding — descriptive only, never quantitative

## 16. Final polish

- [x] 16.1 Verify every Table-of-Contents anchor resolves to an existing heading
- [x] 16.2 Verify each feature-group section contains all three lenses (Human-Language README, Decision Log, Maintenance Guide)
- [x] 16.3 Verify the 👤 / 🛠 convention is used in every lens that involves both audiences
- [x] 16.4 Search the document for excluded content (Cakestore Mommy, separator instructions, watermark, marketing-list how-to, language/translation, IE / browser-floor advice, **any dollar amount or pricing language**) and confirm none is present. Specifically grep for `$`, `USD`, `price`, `paid`, `invoice`, `quote`, `estimate`, and any digits adjacent to a currency symbol — all must be absent
- [x] 16.5 Cross-check the document covers every active capability listed under `openspec/specs/` (14 capabilities — name them in checklist form and tick each one)
- [x] 16.6 Update the front-matter `updated:` date to the actual implementation date
- [x] 16.7 Run the document through a Markdown renderer to confirm it parses cleanly and the Table of Contents renders as expected

## 17. Handoff

- [x] 17.1 Commit `docs/client-readme.md` (and the new `docs/` directory) using a clear conventional-commit message (e.g., `docs(client): add master delivery readme covering all features`)
- [x] 17.2 Verify the file is the only artifact added to the repository — no code, no spec, no `functions.php` edits
- [ ] 17.3 Notify the client that the file is available; share the file as a one-off PDF if requested
