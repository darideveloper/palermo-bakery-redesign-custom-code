## ADDED Requirements

### Requirement: Master client-facing delivery document exists at a known path
The project SHALL maintain a single Markdown file at `docs/client-readme.md` that serves as the master, audience-split delivery record for the Palermo Bakery redesign. The file SHALL exist in a top-level `docs/` directory and SHALL be the canonical document the client receives at handover.

#### Scenario: File exists at the expected path
- **WHEN** a reader inspects the repository tree
- **THEN** the file `docs/client-readme.md` SHALL exist
- **AND** it SHALL be the only Markdown file inside `docs/` for this delivery

#### Scenario: Document opens as the project's narrative entry point
- **WHEN** a non-technical reader opens `docs/client-readme.md`
- **THEN** the first section SHALL be an Executive Summary that explains, in plain English, what the project does and what was delivered
- **AND** a Table of Contents SHALL appear within the first 60 lines of the file

### Requirement: Document follows the agency delivery template's three-lens structure applied per feature group
The document SHALL apply the three-section pattern from `delivery-template.md` — (1) Human-Language README, (2) Decision Log, (3) Maintenance Guide — **per feature group**, not once for the whole project. Every feature group SHALL include all three lenses.

#### Scenario: Each feature group has all three lenses
- **WHEN** a reader inspects any feature-group section of the document (e.g., "Cake Gallery", "Favourites System", "Sharing", "Lightbox", "Order-Wedding-Cake Colour Switch", "Floating Cupcake Form", "Authentication")
- **THEN** that section SHALL contain a Human-Language README subsection (what it does in plain language)
- **AND** a Decision Log subsection (why we built it this way, with rationale for at least one key technical choice)
- **AND** a Maintenance Guide subsection (what the client can do alone, what to be careful of, when to call the developer)

#### Scenario: The Human-Language README of each feature avoids jargon
- **WHEN** a non-technical reader reads any Human-Language README subsection
- **THEN** it SHALL NOT contain unexplained acronyms (CSS, JS, PHP, AJAX, DOM, CSRF, OPcache, MutationObserver, etc.) without a plain-language gloss in the same paragraph

### Requirement: Document enforces explicit audience separation between non-technical and technical content
Every feature-group section SHALL separate non-technical and technical content using two clearly labelled subsections inside each lens: "👤 For the bakery team" and "🛠 For developers". The two SHALL NOT be interleaved within the same paragraph or bullet list.

#### Scenario: Both audience tags appear in each lens that requires both
- **WHEN** a feature involves both an operator concern (e.g., uploading images) and a developer concern (e.g., the MutationObserver in `fav-button.js`)
- **THEN** the bakery-team and developer subsections SHALL each be present and labelled with their emoji prefix
- **AND** the developer subsection SHALL reference the corresponding OpenSpec capability by name when one exists (e.g., "see `gallery-optimization` spec for the full requirement list")

#### Scenario: How-to-read note exists up front
- **WHEN** a reader opens the document
- **THEN** before the Table of Contents the document SHALL include a short "How to read this document" note that explains the 👤 / 🛠 convention

### Requirement: Document includes a quick-reference table of load-bearing constants
The document SHALL include a single reference table near the top, listing every identifier the project depends on whose silent change would break a feature.

#### Scenario: Reference table is complete
- **WHEN** a reader inspects the reference table
- **THEN** the table SHALL list at minimum: Contact Form 7 form id `1874` (Ask-Me popup), WordPress page id `12` / slug `favorite-cakes` (favourites board), WordPress page id `1122` (Venue wedding-cake form), URL slug `cake-gallery` (shop archive), URL prefix `/order-wedding-cake/` (colour-switch page), user-meta key `my_cake_favorites`, WordPress AJAX action names `save_user_favorites` / `get_user_favorites` / `render_favorite_products`, nonce action `cake_fav_nonce`, and the URL query parameter `shared_favs`
- **AND** each entry SHALL state the failure mode if the value changes (e.g., "if this CF7 form id changes, the popup will no longer auto-close after submission")

### Requirement: Document includes the complete plugin and theme dependency manifest
The document SHALL include a "Required modules" section that lists every WordPress plugin and theme the project depends on to function, with the role each plays.

#### Scenario: Every load-bearing dependency is listed
- **WHEN** a reader inspects the Required modules section
- **THEN** the following dependencies SHALL be listed by name and role: WordPress, WooCommerce, the SNS Vicky theme (with the note that only `header-style4` is supported), the Simple Custom CSS and JS plugin (delivery vehicle for all custom code), Contact Form 7, Theme My Login, YITH WooCommerce Wishlist (read for the product-ID fragment reference), WPBakery / Visual Composer (page-builder used by the client), and WPvivid Backup (the client's existing backup tool)
- **AND** each entry SHALL state which feature(s) break if the dependency is deactivated

### Requirement: Document records the cake-image upload conventions and freezes legacy exceptions
The document SHALL state the image-upload conventions that operators must follow, and SHALL explicitly mark the four legacy filename exceptions in the colour-switch script as frozen — not to be expanded.

#### Scenario: Convention is stated clearly for new uploads
- **WHEN** a bakery operator reads the image-upload conventions
- **THEN** the document SHALL state that products are uploaded as normal WooCommerce products
- **AND** that all product images should be on a plain white background
- **AND** that for the order-wedding-cake page the two variants share an identical filename except for the `white` ↔ `ivory` token
- **AND** that both variants must be uploaded in the same file format (both `.jpg` or both `.png`)

#### Scenario: Legacy exceptions are listed and marked frozen
- **WHEN** a reader inspects the colour-switch maintenance subsection
- **THEN** the document SHALL list the four legacy exceptions: Rustic-Stucco (`5RusticStuccoIvoryWeddingCake` ↔ `5WhiteStuccoIvoryWeddingCake`), Pindots (`3PindotsIvoryWeddingCake.jpg` ↔ `WPindots.png`), Screen-Shot (`...10.58.40-PM` ↔ `...10.58.32-PM`), and the permanent-white `11ExquisiteWhiteWeddingCake`
- **AND** SHALL state explicitly that this list is frozen and will not be expanded — new cakes that do not follow the naming convention will not be auto-swapped

### Requirement: Document includes a future-enhancements section listing deferred items
The document SHALL include a clearly-labelled "Future enhancements (not yet delivered)" section that lists items previously discussed but explicitly out of scope of the current delivery.

#### Scenario: Deferred items are present and disclaimed
- **WHEN** a reader inspects the future-enhancements section
- **THEN** that section SHALL open with a disclaimer that nothing in it is delivered today and each item requires a separate scope
- **AND** the section SHALL list at minimum: email-channel sharing for boards, consultant address book, ESP / marketing-list integration, multi-language (i18n) support, third colour option on the wedding-cake switcher, an optional background-removal local script with tutorial as an extra
- **AND** SHALL NOT contain pricing, delivery dates, or any language that could be interpreted as a binding commitment

#### Scenario: Removed historical features are not listed as future enhancements
- **WHEN** a reader inspects the future-enhancements section
- **THEN** the daridev mail-API integration, the alternating WebP separator system, watermark stripping, and the Cakestore Mommy design reference SHALL NOT appear
- **AND** these removed features SHALL NOT appear anywhere else in the document either

### Requirement: Document covers every current OpenSpec capability through a corresponding feature group
The document SHALL ensure every active OpenSpec capability under `openspec/specs/` is reflected in at least one feature group, so that the master delivery record is complete with respect to the technical specification set.

#### Scenario: All current capabilities are covered
- **WHEN** a reader cross-references the document's feature-group sections with the list of folders under `openspec/specs/`
- **THEN** each of the following capabilities SHALL be addressed in at least one feature-group section: `project-organization`, `auth-form-layout`, `form-frontend`, `gallery-optimization`, `lightbox-close-redirection`, `lightbox-fav-button`, `lightbox-share-button`, `gallery-auth-buttons`, `gallery-fav-pill`, `favorites-lightbox`, `favorites-heart-button`, `favorites-share-button`, `favorites-shared-save-feedback`, `favorites-full-size-images`

#### Scenario: The site-mode-lock-down behaviour is documented
- **WHEN** a reader inspects the gallery feature group
- **THEN** the document SHALL explain that single product pages are disabled and that any direct access redirects to the gallery, even though that behaviour is not currently bound to a named OpenSpec capability

#### Scenario: The order-wedding-cake colour switcher is documented
- **WHEN** a reader inspects the order-wedding-cake feature group
- **THEN** the document SHALL explain the White-default behaviour, the exclusive-checkbox behaviour, the filename-rewriting logic, and the dimension-suffix stripping, even though those are not currently bound to a named OpenSpec capability

### Requirement: Document includes an operational maintenance guide tailored to WP Engine
The document SHALL include an operational guide that names the specific tools and constraints of the live WP Engine install, not generic WordPress maintenance advice.

#### Scenario: WP Engine specifics appear in the maintenance guide
- **WHEN** a reader needs to edit `functions.php` on the live site
- **THEN** the document SHALL instruct the reader to use the WP File Manager plugin against `/wp-content/themes/snsvicky/functions.php` rather than the WordPress Theme File Editor
- **AND** SHALL state that OPcache must be manually invalidated after PHP edits
- **AND** SHALL document the temporary `?palermo_reset_opcache=1` snippet as the fallback if portal access fails

#### Scenario: Caching guidance for QA appears
- **WHEN** a reader needs to verify a change on the live site
- **THEN** the document SHALL instruct them to use a Private or Incognito browser window to bypass WP Engine and LiteSpeed caches

#### Scenario: Backup guidance appears before major updates
- **WHEN** a reader plans a WordPress or WooCommerce update
- **THEN** the document SHALL instruct them to run a WPvivid backup first

### Requirement: Document excludes specific content known to be out of scope
The document SHALL NOT contain content that the project owner has explicitly excluded.

#### Scenario: Excluded content is absent
- **WHEN** a reader searches the document for any of the following terms or topics
- **THEN** none of the following SHALL appear: "Cakestore Mommy" or any design-inspiration attribution; instructions for adding decorative section separators (manual or page-builder based); watermark-removal instructions; instructions for connecting an ESP or marketing-list service; instructions for adding new languages or translation files; advice about IE / legacy-browser support; instructions for expanding the colour-switch filename-exception map beyond its current four entries

### Requirement: Document is free of all pricing and commercial figures
The document SHALL NOT contain any pricing information, payment amounts, invoice references, hourly rates, time estimates expressed as money, or any commercial figures of any kind. Commercial details are handled privately between developer and client and never enter the documentation. This rule applies to every section, including the future-enhancements section, the per-feature decision logs, and the maintenance guide.

#### Scenario: No dollar amounts appear anywhere
- **WHEN** a reader greps the document for the `$` character, the string `USD`, or any sequence of digits adjacent to a currency symbol
- **THEN** zero matches SHALL be found

#### Scenario: No pricing-adjacent language appears anywhere
- **WHEN** a reader greps the document for the words `price`, `priced`, `pricing`, `quote`, `quoted`, `estimate`, `paid`, `payment`, `invoice`, `cost`, `costs`, `fee`, `fees`, `rate`, or `rates` used in a commercial sense
- **THEN** zero such commercial usages SHALL be found
- **AND** the document MAY still use these words in non-commercial senses where unavoidable (e.g., "data rate", "estimate the impact") provided the meaning is unambiguously technical

#### Scenario: Future-enhancements items carry descriptive scope only
- **WHEN** a reader inspects any item in the future-enhancements section
- **THEN** the item description SHALL be qualitative and descriptive only
- **AND** SHALL NOT include any number that could be interpreted as a price, hours estimate, day estimate, or other commercial figure

### Requirement: Document is self-contained and does not require reading OpenSpec to operate the site
The document SHALL be readable end-to-end without opening any file under `openspec/`. Cross-references to OpenSpec exist only as developer-facing pointers, never as load-bearing context the non-technical reader is required to follow.

#### Scenario: A non-technical reader can complete a routine operation using only this document
- **WHEN** a non-technical reader needs to add a new cake or edit the Ask-Me popup's fields
- **THEN** the document SHALL contain step-by-step instructions sufficient to complete the operation without opening any other file in the repository
- **AND** such instructions SHALL live in the 👤 subsection of the relevant feature group
