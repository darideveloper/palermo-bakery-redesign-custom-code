---
created: 2026-05-17
updated: 2026-05-17
tags:
  - work
  - client-docs
  - palermo-bakery
type: area-note
status: active
---

# Palermo Bakery Redesign — Master Delivery Document

## Executive Summary

Your WordPress site has been transformed from a standard online shop into a polished visual gallery for Palermo Bakery, with a complete favourites and sharing system, an interactive wedding-cake colour picker, and a discreet "Ask Me" contact widget. The work was delivered in three phases — **Minor site updates** (cleanups and small fixes), **Favourites & Sharing** (the heart-button system, the favourites board, and the shareable links), and **Auto-change Cake Images** (the White/Ivory toggle on the wedding-cake order page). Everything is live on `ccdev2026.wpenginepowered.com` and ready for production.

---

## How to read this document

This document has two audiences and is marked accordingly throughout:

- 👤 **For the bakery team** — plain-English explanations of what each feature does, how to operate it, and what to watch out for. No code knowledge required.
- 🛠 **For developers** — technical wiring details, file paths, function names, and pointers to the OpenSpec capabilities that bind the behaviour formally. Skip these sections if you're not editing code.

Both audiences should read the **Executive Summary**, the **Quick-reference table**, and the **Required modules** section. Everything else can be read as needed.

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [How to read this document](#how-to-read-this-document)
3. [Quick-reference table — load-bearing constants](#quick-reference-table--load-bearing-constants)
4. [Required modules](#required-modules)
5. [Feature group — Cake Gallery](#feature-group--cake-gallery)
6. [Feature group — Favourites System](#feature-group--favourites-system)
7. [Feature group — Sharing System](#feature-group--sharing-system)
8. [Feature group — Custom Lightbox](#feature-group--custom-lightbox)
9. [Feature group — Authentication](#feature-group--authentication)
10. [Feature group — Ask-Me Cupcake Form](#feature-group--ask-me-cupcake-form)
11. [Feature group — Venue Wedding Cake Form & Footer Form](#feature-group--venue-wedding-cake-form--footer-form)
12. [Feature group — Order-Wedding-Cake Colour Switch](#feature-group--order-wedding-cake-colour-switch)
13. [Feature group — Branding & Layout](#feature-group--branding--layout)
14. [Operational maintenance guide (WP Engine specifics)](#operational-maintenance-guide-wp-engine-specifics)
15. [Future enhancements (not yet delivered)](#future-enhancements-not-yet-delivered)

---

## Quick-reference table — load-bearing constants

These are the IDs, slugs, and names the project depends on. **Do not change any of them in WordPress without telling your developer first** — most of them are silent failure points (the feature stops working but no error appears in the admin).

| Type | Value | Where it's used | What breaks if you change it |
| --- | --- | --- | --- |
| Contact Form 7 form ID | `1874` | The "Ask Me" floating cupcake popup | The popup will still open, but it will no longer auto-close after a successful submission; and the form-relocation script may fail to find the form to move into the popup. |
| WordPress page ID | `12` | The favourites board | The "Favorite Cakes" page styles (sidebar removal, centred title, force-loaded lightbox library) only target page id 12. If the page is re-created with a different id, the favourites board will look unstyled and the lightbox won't open. |
| URL slug | `favorite-cakes` | The favourites board URL (`/favorite-cakes/`) | Share links of the form `/favorite-cakes/?shared_favs=…` will 404. The "♥ Favorite Cakes" pill in the gallery filter row will point to a dead URL. |
| WordPress page ID | `1122` | The Venue Wedding Cake form page | The polished form styling (2-column grid, file-upload drop-zone, image CAPTCHA, alignment fixes) is scoped to page id 1122. If the page is rebuilt with a different id, the form will look unstyled. |
| URL slug | `cake-gallery` | The main shop archive (the cake gallery page) | The iOS performance fixes, the gallery-only mode, the auth pills, and the "♥ Favorite Cakes" filter pill are all activated by the `cake-gallery` slug. If renamed, the gallery may crash on iPhones again. |
| URL prefix | `/order-wedding-cake/` | The wedding-cake order form page | The White/Ivory image-swap script only activates on pages whose form `action` starts with `/order-wedding-cake/`. Rename and the colour switcher silently stops working. |
| User meta key | `my_cake_favorites` | Per-user persistent favourites in the database | If renamed, every logged-in user appears to lose their saved favourites (the data still exists under the old key but the new code can't see it). |
| WordPress AJAX action | `save_user_favorites` | Save the user's favourites to the database | Hearts on cards will still light up locally, but they won't persist across devices or sessions. |
| WordPress AJAX action | `get_user_favorites` | Retrieve a user's saved favourites on login | After logging in on a new device, the user's saved favourites won't appear — only what's in that browser's local storage. |
| WordPress AJAX action | `render_favorite_products` | Render the favourites masonry grid via AJAX | The favourites board will show "Loading your favorite cakes…" forever. |
| Nonce action | `cake_fav_nonce` | Security check for every favourites AJAX call | All favourites AJAX calls fail silently with a security error. |
| URL query parameter | `shared_favs` | Activates the "Cakes Shared With You" section on the favourites page | Existing share links break (recipients see an empty favourites board instead of the shared selection). |

---

## Required modules

The site needs these WordPress plugins and themes to be active. **As of delivery on 2026-05-17**, every item below is installed and configured on `ccdev2026.wpenginepowered.com`. Always confirm in WP admin before troubleshooting.

| Module | Role | What breaks if deactivated |
| --- | --- | --- |
| **WordPress** | The platform | Everything. |
| **WooCommerce** | Product / cake catalogue management | The whole gallery disappears — the gallery is built from WooCommerce products. |
| **SNS Vicky theme** | The active theme. **Only `header-style4` is supported.** | The site falls back to a default theme; all custom styling, mega-menu, sidebars, and the header layout break. |
| **Simple Custom CSS and JS** plugin | Delivery vehicle for **all** custom CSS and JS in this project | Every custom feature stops working (gallery cleanup, favourites, sharing, lightbox tweaks, colour switcher, cupcake popup, brand logo fix). |
| **Contact Form 7** | The form engine behind the cupcake popup and the venue wedding-cake form | The "Ask Me" popup shows nothing inside, and the wedding-cake order form is unusable. |
| **Theme My Login** | Front-end `/login`, `/register`, `/lost-password` pages | Users can't register or log in from the front of the site; favourites stop syncing across devices for new users. |
| **YITH WooCommerce Wishlist** | Source of the product ID we attach to each gallery heart | The favourites buttons on gallery cards can't identify which cake you're trying to save, so the heart no longer works on the gallery. (The favourites page still works.) |
| **WPBakery / Visual Composer** | The page builder the bakery uses to lay out marketing pages | Long-form pages (home, about, landing pages) lose their layout. Custom-code features still work. |
| **WPvivid Backup** | The backup plugin you already have | Nothing breaks day-to-day, but you'll have no recovery point if a major update goes wrong. Always click **Backup Now** before WordPress / WooCommerce major updates. |

---

## Feature group — Cake Gallery

The visual heart of the project. The standard WooCommerce shop has been rebuilt as a clean, gallery-style display with no buying chrome, no single-product pages, and a custom lightbox.

### Human-Language README

👤 **For the bakery team:**

The page at **`/cake-gallery/`** shows all your cakes as a tidy grid of square images. Hover over a cake on a computer to see a gentle zoom; tap one on a phone to open a large, easy-to-browse popup window with arrow buttons to flip through every cake. Clicking the title at the top of the popup opens that cake's full WooCommerce product page in a new tab (where the standard product layout is used, including title, price, "Add to cart", description, and related products). The popup itself has no buying chrome — it's purely for browsing the gallery.

Above the grid you'll see a row of **category pills** ("All", "Wedding", "Birthday"…) plus a "♥ Favorite Cakes" pill on the far left. Guests (people not signed in) also see **Login** and **Sign Up** pills there. Click any pill to filter; a soft loading spinner appears while the page reloads.

🛠 **For developers:**

The gallery is the WooCommerce shop archive (template `archive-product.php` from the SNS Vicky theme) but heavily reskinned by `src/features/gallery/product-gallery.css` and `src/features/lightbox/image-lightbox.js`. Single-product pages are reachable directly (the previous `template_redirect` redirect was removed); the gallery remains the primary entry point and the standard WC single-product template renders for any direct permalink. The lightbox title `.ppt` is converted into a clickable permalink link by `src/features/favorites/fav-button.js` (openSpec capability: `lightbox-title-permalink-link`); the product permalink is sourced from a `data-product-permalink` attribute on the gallery card anchor, populated by the image-rewrite output buffer in `src/core/functions.php`.

> **Shop-archive blank-page fix (2026-08):** `/cake-gallery/` is the shop archive, which lists all products (349). The YITH pre-pass that stamps `data-product-permalink` onto each card previously used a lazy `.*?` regex; on the full shop archive the footer `yith_wcwl_l10n` inline script grows to ~1.1MB, which blew past `pcre.backtrack_limit` (1M), `preg_replace_callback` returned `null`, and the whole page rendered blank. The regex is now an unrolled loop (`[^<]*(?:<(?!…)[^<]*)*`) plus a null guard, mirroring the stripSuffix rewrite. If `/cake-gallery/` ever comes back empty after a deploy, check this pre-pass first.

Three OpenSpec capabilities govern the gallery's behaviour:
- `gallery-optimization` — the iOS Safari performance contract (full-res gallery images, sentinels, lazy-loading, third-party-script removal).
- `gallery-auth-buttons` — Login/Sign-Up pills for guests, output via `woocommerce_before_shop_loop`.
- `gallery-fav-pill` — the "♥ Favorite Cakes" pill injected as the first `<li>` in the category filter row.

### Decision Log

👤 **For the bakery team:**

We chose to make the site a **gallery-first** experience (no shopping cart, no price tags on the gallery cards) because that's how the bakery's sales flow actually works — clients see cakes, then book a consultation. The gallery itself stays image-only, but single-product pages are reachable (either directly via permalink or by clicking the title in the lightbox) and use the standard WooCommerce single-product template. Showing an "Add to cart" button on a wedding cake would be misleading in the gallery view, so it's hidden there; the standard WC buying flow is available on the product page itself.

🛠 **For developers:**

The deep technical decision tree centres on the iPhone Safari freeze that emerged after 297 products were uploaded. The fix is layered:

1. **Server-side image rewrite.** A `template_redirect` output buffer in `src/core/functions.php` (~line 1192) rewrites every gallery `<img>` so the `src` and `data-original` point at the full-resolution image (no dimension-suffix downgrade), adds `loading="lazy"` and `decoding="async"`, strips the `lazy` CSS class (so the theme's `jquery.lazyload` scroll handler ignores them), and removes `srcset` entirely.
2. **URL sentinels.** Gallery images get `?t=300` appended to `src`/`data-original`; the lightbox source gets `?l=1` appended (stored in a `data-lightbox-src` attribute). These literal query strings defeat a rogue URL-rewriter regex anchored to `.jpg$`/`.png$`/`.webp$`. WordPress ignores unknown query params, so the same image bytes are served either way.
3. **Rogue script removal.** The same output buffer strips inline `<script>` blocks containing `const stripSuffix` or `const updateImagesAndHide`, declared by the "Simple Custom CSS and JS" plugin from a previous developer that was forcing image re-fetches every 450 ms.
4. **Third-party blocker dequeue.** A `wp_enqueue_scripts` hook at priority 100 dequeues `wc-cart-fragments`, `wc-add-to-cart`, the WordPress emoji actions, and strips an inline reCAPTCHA `<script>` — all of which were preventing the browser `load` event from firing on iPhones.
5. **Gallery-view predicate.** A helper `_palermo_is_gallery_view()` returns `is_shop() || is_product_category() || is_product_tag() || is_page('cake-gallery')`. Every gallery-only hook guards with this. `is_page('cake-gallery')` alone is insufficient — `/cake-gallery/` is the shop archive, where `is_page()` returns false.
6. **Chunked card prep.** `image-lightbox.js` runs `prepareCard()` in batches of 30 via `requestAnimationFrame` so iOS Safari can paint and respond to touch between batches. Above-the-fold cards are processed synchronously for a fast first paint.

The full requirement list lives in `openspec/specs/gallery-optimization/spec.md`.

### Maintenance Guide

👤 **For the bakery team:**

**Adding a new cake.** Add it as a normal WooCommerce product — same workflow you've always used. The image should be on a **plain white background** to blend with the gallery. The grid automatically picks it up; no developer needed.

**The gallery looks wrong** (broken layout, hot pink backgrounds, etc.). Usually that's a browser cache problem. Open the site in a **Private / Incognito** window first. If it's still broken, take a screenshot and contact your developer.

**Someone reported the site froze on their iPhone.** This was the original problem the gallery was rebuilt to solve. If it comes back, it usually means a plugin update has re-enabled WooCommerce's cart-fragments script or another plugin has injected a competing rewrite script — contact your developer.

🛠 **For developers:**

To edit `functions.php` on the live site you must use **WP File Manager** plugin (`/wp-content/themes/snsvicky/functions.php`). The WordPress Theme File Editor on this WP Engine deployment writes to a shadow path and does not affect the running site. After editing, OPcache must be invalidated — see the [Operational maintenance guide](#operational-maintenance-guide-wp-engine-specifics) section.

The single-product pages are reachable directly. If you ever need to preview a single product for testing, just visit the product's permalink — `/product/<slug>/` — in an incognito window.

---

## Feature group — Favourites System

A "Pinterest-style" bookmarking system that lets visitors save the cakes they like — locally if they're guests, persistently across devices once they sign in.

### Human-Language README

👤 **For the bakery team:**

Every cake card in the gallery has a small white **heart button** in its top-right corner. Tap it once and the heart turns red (❤️). Tap again and it goes back to white (🤍). The number of hearts you've saved appears as a badge on the **cake icon** in the top-right of the main header — that's the same icon any visitor can click to jump to their personal **Favourites board** at `/favorite-cakes/`.

The board itself is a clean, 3-column tiled gallery (2 columns on tablets, 1 column on phones). Tap any cake to see it full-size in the popup window. Tap the red heart on the board to remove that cake — it fades out smoothly.

**The clever part:** if you favourite cakes on your phone *without an account*, they live in your phone's browser memory. The moment you sign up or log in, those favourites are automatically merged with whatever is already saved to your account, and from then on they're synced across every device you log into.

🛠 **For developers:**

Client-side state lives in `localStorage` under the key `my_cake_favs`. Server-side state lives in user meta `my_cake_favorites` (note the singular/plural mismatch is historical and intentional — don't "fix" it). On login, `fav-button.js → initFavorites()` calls the `get_user_favorites` AJAX action, merges the result with localStorage via a `Set`, writes the merged list back to localStorage, calls `updateUI(mergedFavs)`, and if the local list had more items than the server's it pushes the merged list back via `save_user_favorites`.

Hearts are injected onto every `.product-inner` card by a MutationObserver (`injectHeartButtons()`) — necessary because the YITH wishlist element that holds the product ID may not be present at DOM-ready time, and because the gallery's category filter re-renders the product grid via AJAX.

Capabilities involved:
- `favorites-heart-button` — the ❤️ on cards and on the favourites board.
- `favorites-lightbox` — prettyPhoto on the favourites page.
- `favorites-full-size-images` — full-resolution (`'full'`) image rendering for high-quality lightbox views.
- `favorites-shared-save-feedback` — fade-out of the save button after a shared cake is saved.

### Decision Log

👤 **For the bakery team:**

We chose to support **both** guest favourites (browser-only) and account favourites (database, cross-device) because most visitors won't sign up until they've already saved a few cakes — and we didn't want them to lose those when they finally registered. So the system "carries" guest selections forward into the new account automatically.

We chose **WooCommerce user meta** (a built-in WordPress storage mechanism) rather than a separate database table for two reasons: it's invisible to plugin conflicts, and it travels cleanly with WordPress backups.

🛠 **For developers:**

- **Source of truth = localStorage.** The server is the persistence layer, not the source of truth. This keeps hearts feeling instant — there's no waiting for the network on a click. The server sync happens in the background.
- **`cakeFavsData` head bootstrap.** `inject_cake_favs_data()` in `src/core/functions.php` writes an inline `<script>` into `<head>` exposing `ajaxUrl`, `nonce`, and `isLoggedIn`. The JS files don't need to be re-built when the AJAX URL changes (it's a constant on this WP install, but the pattern keeps things portable).
- **MutationObserver heart injection** (`injectHeartButtons()` in `src/features/favorites/fav-button.js`). Hearts are added dynamically because the YITH wishlist element (`.yith-wcwl-add-to-wishlist[data-fragment-ref]`) is what carries the product ID. We read that, then layer our own heart on top, while CSS hides the YITH default button.
- **Hybrid card source.** On the gallery, the product ID comes from the YITH element. On the favourites board, the product ID is encoded directly on the masonry-item link (`data-product-id`). Both code paths exist in `getLightboxProductId()` and the master click listener.

### Maintenance Guide

👤 **For the bakery team:**

**A cake is removed from the catalogue.** Anyone who had it favourited will simply see one less card on their board the next time they load it — it disappears silently, no error.

**A user reports their favourites are gone.** Check:
1. Are they logged in? Guest favourites are device-specific; logging out doesn't wipe them, but switching browsers / clearing data does.
2. Are they on the right account? Check the email in the top-right account menu.
3. As a last resort, you can check the database directly — see the developer section below for the query.

**The empty state** ("Your favorites list is empty.") appears when a user has no favourites. This is expected.

🛠 **For developers:**

To check a user's saved favourites directly:

```sql
SELECT meta_value FROM wp_usermeta
WHERE user_id = <id> AND meta_key = 'my_cake_favorites';
```

You'll get a CSV of WooCommerce product IDs.

If page id 12 ever changes (e.g., the page is rebuilt from scratch), update the `wp_enqueue_scripts` hook in `src/core/functions.php` that force-loads prettyPhoto:

```php
if (!is_page('favorite-cakes') && !is_page(12)) return;
```

Change `12` to the new page ID, or rely on the slug check alone if you keep the slug stable.

---

## Feature group — Sharing System

Two ways to share: a single cake from the lightbox, or your entire favourites board from the favourites page.

### Human-Language README

👤 **For the bakery team:**

**Sharing a single cake.** When you open any cake in the popup window, you'll see two small white buttons at the bottom-centre of the image — a heart on the right and a **share icon (📤-style)** on the left. Tap the share icon. The system copies a link to your clipboard and shows a tiny black "Link Copied!" message. Paste that link anywhere (a text, an email, WhatsApp). When the recipient opens it, they see your selected cake on a special "Cakes Shared With You" section on your favourites page.

**Sharing your whole board.** Open the favourites page. Below the grid is a black pill-shaped button labelled **📤 Share My Favorites**. Tap it. The system copies a link representing your entire saved collection. The button briefly says **✅ Link Copied!** for confirmation, then returns to its normal label.

**What a recipient sees.** They land on `/favorite-cakes/` with a special "Cakes Shared With You" section at the top showing only the cakes you shared. They can browse them, tap to open in the popup, and **save any of them to their *own* favourites** by tapping the heart button on each shared card — those go into the recipient's own collection, not yours. **They can't add anything to your list or take anything away from it.** Read-only with respect to the sender; full read/write on their own board.

🛠 **For developers:**

The URL contract is plain: `/favorite-cakes/?shared_favs=<csv of product IDs>`. The recipient's `fav-button.js` parses the query parameter on load, calls `renderGrid(sharedIds, "shared-cakes-list", true)`, and reveals the `#shared-section` div. The `is_shared=true` flag in the AJAX call tells PHP to render `.save-shared-btn` (save-to-my-favs) instead of `.my-custom-fav-btn` (heart toggle).

Capabilities involved:
- `lightbox-share-button` — the in-lightbox share icon and clipboard-copy.
- `favorites-share-button` — the page-level "Share My Favorites" pill.

### Decision Log

👤 **For the bakery team:**

We deliberately built sharing as **read-only with respect to the sender**. The recipient sees what you sent, can save individual cakes to their own list, but can't reach back and modify your collection. That keeps the sender in full control. The recipient gets their own independent space where they can curate alternatives if they want.

We chose **copy-to-clipboard** rather than building in social-media buttons (Facebook, Twitter) or email integration because the share menu of every modern phone and browser already handles that. One copy → paste anywhere.

🛠 **For developers:**

- **Read-only enforced at the mutation boundary, not the URL.** The URL just encodes which IDs to display. The `save_user_favorites` AJAX endpoint requires `is_user_logged_in()` and writes only to *the current user's* meta — never the sender's. So even if a malicious recipient figures out the URL pattern, they can only modify their own list.
- **Share URLs are intentionally public and guessable.** `?shared_favs=12,34,56` is plain integer CSV. This is acknowledged as a trade-off, not an oversight: there is no privacy expectation on a bakery gallery, so we accepted the simplicity in exchange for transparency.
- **Both share buttons use the same URL format.** Single-cake: `?shared_favs=<id>`. Whole board: `?shared_favs=<id>,<id>,<id>`. Same renderer either way.
- **Clipboard feedback preserves layout.** The page-level button swaps two child spans (`.share-btn-icon` + `.share-btn-text`) — never the whole `innerHTML` — so the pill shape and flex alignment never shift during the 2-second feedback window.

### Maintenance Guide

👤 **For the bakery team:**

**How to test a share link.**
1. Sign in as yourself.
2. Favourite 2-3 cakes.
3. Open one in the popup, click the share icon. Paste the link into a separate browser (or a private window).
4. You should see the "Cakes Shared With You" section showing the cake you shared.
5. Then click the page-level "📤 Share My Favorites" button on your own page; repeat the paste-in-another-window test. You should now see all your favourites in the shared section.

**A share link goes to an empty page.** Check that the URL has `?shared_favs=` followed by at least one number. If the recipient's browser dropped the query string for any reason, the page still loads but the section stays hidden.

🛠 **For developers:**

To extend the URL contract without breaking existing shared links, **add** new optional query parameters — never repurpose or remove `shared_favs`. The recipient-side parser uses `URLSearchParams.get('shared_favs')` and treats absent or empty as "no shared section", so additional parameters are safely ignored by older links.

---

## Feature group — Custom Lightbox

The popup window that opens when you click any cake — used in three contexts: the gallery, the favourites board, and shared selections.

### Human-Language README

👤 **For the bakery team:**

Tap any cake in the gallery or favourites page and a large popup opens showing the cake at full quality. Behind it the page dims. To navigate between cakes use the **large arrow areas on the left and right** (just tap anywhere on the left or right half of the image). Close the popup by tapping the **× in the top-right** or anywhere on the dark background.

Inside the popup, near the bottom-centre of the image, you'll see the **share icon** (left) and **heart button** (right). Both work the same as on the cards — the heart saves or unsaves the cake; the share icon copies a link to that one specific cake.

The **title at the top of the popup** (e.g. "Tuxedo Birthday Cake") is a clickable link to that cake's full product page — clicking it opens the product page in a new browser tab, while the gallery stays exactly where you left it. If the title is plain text instead of a link, the system couldn't resolve the product permalink for that cake (most likely YITH is missing on that card); tell your developer which cake is affected.

There is no thumbnail strip at the bottom (we removed it to keep the focus on the cake), no "expand" button, and no social-media share buttons (we use our own share system instead).

🛠 **For developers:**

Uses the `prettyPhoto` library bundled with WooCommerce. Configuration in `src/features/lightbox/image-lightbox.js`:

```js
var PRETTYPHOTO_OPTIONS = {
  hook: "data-rel",
  social_tools: false,
  theme: "pp_default",
  horizontal_padding: 20,
  opacity: 0.8,
  deeplinking: false,
  allow_resize: true,
  default_width: 900,
  default_height: 600,
  overlay_gallery: false,
  changepicturecallback: function () {
    var viewportHeight = $(window).height();
    $(".pp_content_container").css("max-height", viewportHeight - 120 + "px");
  },
};
```

Two scoped groups keep navigation local to each surface:
- `prettyPhoto[cake-gallery]` for the shop archive.
- `prettyPhoto[fav-gallery]` for the favourites board (so arrows on the favourites page only cycle through favourited cakes).

Capabilities involved:
- `lightbox-close-redirection` — `.pp_close` clicks are intercepted in capture phase and forwarded to `.pp_overlay`.
- `lightbox-fav-button` — the heart inside the lightbox.
- `lightbox-share-button` — the share icon inside the lightbox.
- `lightbox-title-permalink-link` — the `.ppt` title inside the lightbox is converted into a clickable link to the product permalink.

### Decision Log

👤 **For the bakery team:**

We hid the thumbnail strip (the row of small images at the bottom of the old popup) because it cluttered the view and didn't help with discovery — clients want to look at one cake at a time. We hid the "expand" button (it does the same thing as just opening the popup again) for the same reason. We replaced the basic Facebook/Twitter sharing with our own share button so we control exactly what a shared link does.

🛠 **For developers:**

- **Close → overlay redirect** (in capture phase) unifies the close behaviour. The native `.pp_close` handler sometimes leaves the lightbox in a bad state on iOS Safari; routing through `.pp_overlay`'s click is what the library uses internally for "click outside to close" and it handles state correctly.
- **Vertical arrow centring** (`top: 50%; transform: translateY(-50%)`) was specifically requested for thumb-reach on mobile. The arrows themselves are invisible — the entire left and right halves of the image are 50%-wide hit-areas; the visible chevron is just an `::after` pseudo-element in the centre of each hit-area.
- **`changepicturecallback`** clamps `.pp_content_container` to `viewportHeight − 120` so on phones the image never extends past the visible area (`120px` accounts for the close button + safe-area).

### Maintenance Guide

👤 **For the bakery team:**

**The lightbox stops opening on a new page.** Most likely cause: the new page isn't the cake gallery or the favourites page (those are the only pages where the lightbox library is loaded). Tell your developer the page slug and they'll force-load the library.

**The lightbox title shows as plain text instead of a clickable link.** The JS could not resolve the product permalink for the cake currently shown. Most likely cause: the YITH wishlist element is missing on that card (or has no `data-fragment-ref`). The image-src match is the primary resolution path; if it fails, the title falls back to plain text rather than emit a broken anchor. Tell your developer which cake is affected so they can verify YITH is configured for that product.

**Arrow areas feel unresponsive on a desktop.** Usually a CSS conflict from a recently activated plugin. Try deactivating recent plugins one at a time in incognito mode.

🛠 **For developers:**

To enable the lightbox on a brand-new page (other than the gallery or favourites), force-enqueue prettyPhoto in `src/core/functions.php`:

```php
add_action('wp_enqueue_scripts', function () {
    if (!is_page('your-new-slug')) return;
    $wc_url = plugins_url('', WC_PLUGIN_FILE);
    wp_enqueue_script('prettyPhoto', $wc_url . '/assets/js/prettyPhoto/jquery.prettyPhoto.min.js', array('jquery'), '3.1.6', true);
    wp_enqueue_style('woocommerce_prettyPhoto_css', $wc_url . '/assets/css/prettyPhoto.css', array(), '3.1.6');
}, 20);
```

Then ensure your new page's image links carry `data-rel="prettyPhoto[your-group]"` so they're picked up by the binding logic.

---

## Feature group — Authentication

The `/login`, `/register`, and `/lost-password` pages, plus what we do with the email addresses we collect.

### Human-Language README

👤 **For the bakery team:**

You have **proper user accounts** on the site now. Visitors can register at `/register` with their email, set a password, and from then on their favourites are saved to that account — meaning they can log in on their phone, save 30 cakes, log in on their laptop later, and see the same 30 cakes. The login form is centred on the page with a comfortable max-width — it looks polished rather than stretched edge-to-edge.

**What we do with the emails:** they sit in the standard WordPress user list. Today this is a manual list — you can view it in WP admin under **Users**, sort by registration date, and export to CSV if you ever want to send a newsletter. Automated newsletter integration is **not** delivered today (it's listed in [Future enhancements](#future-enhancements-not-yet-delivered)).

🛠 **For developers:**

Theme My Login (TML) plugin handles the front-end forms. `src/features/auth/custom-auth.css` simply constrains the `.tml` container:

```css
.tml { max-width: 600px; margin: 50px auto !important; }
.tml.tml-register { padding: 0 20px; }
```

Capability: `auth-form-layout`.

The cross-device sync logic isn't in the auth files themselves — it's in `fav-button.js → initFavorites()` (see the [Favourites](#feature-group--favourites-system) section's developer log).

### Decision Log

👤 **For the bakery team:**

We chose **real WordPress accounts** rather than "guest favourites only" because the bakery team wanted to build an email list as a side benefit, and because most users expected their saved cakes to follow them between phone and laptop. Guest favourites still work — but they live only in that one browser.

🛠 **For developers:**

- **No new tables.** Everything uses `wp_users` and `wp_usermeta`. This is intentional: it survives WP and WC core updates with no migration, and it appears in standard WP admin tooling.
- **Merge-on-login is non-destructive.** If the guest had 5 favourites in localStorage and the account had 3 favourites server-side, the merged list is the union (8 unique). If the guest list is *larger* than the server's, the merged list is pushed back to the server so the account "catches up" — otherwise the merge is local-only and the server stays authoritative.

### Maintenance Guide

👤 **For the bakery team:**

**To view the user list:** WP admin → **Users** → All Users. The "Registered" column shows when each visitor signed up. To export, install the free **Users Customers Import Export for Wp Woocommerce** plugin (or any equivalent), filter by your desired date range, and export to CSV. You then have a list you can paste into MailerLite, Mailchimp, Klaviyo, etc.

**A user can't log in.** First step: ask them to use "Forgot password" at `/lost-password`. Second step: in WP admin → Users → click their name → Set New Password.

🛠 **For developers:**

Quick query to enumerate users with saved favourites:

```sql
SELECT u.ID, u.user_email, m.meta_value AS favorites_csv
FROM wp_users u
JOIN wp_usermeta m ON m.user_id = u.ID
WHERE m.meta_key = 'my_cake_favorites'
  AND m.meta_value <> ''
ORDER BY u.user_registered DESC;
```

---

## Feature group — Ask-Me Cupcake Form

The floating cupcake widget that appears on every page of the site.

### Human-Language README

👤 **For the bakery team:**

A **small cupcake graphic with a little toothpick flag that says "Ask Me"** sits permanently in the bottom-right corner of every page. Click it. A modal appears with a short message explaining clients can fill out the form to be contacted by a cake consultant within 24-48 hours. The form collects **Name, Email, Phone, and Message**. Submit, wait for the green confirmation, and the popup closes itself after 3 seconds.

On a phone, the popup goes full-screen for easy typing. On a desktop, it sits as a polished card next to the cupcake.

🛠 **For developers:**

`src/features/popup-form/custom-popup-form.js` injects the cupcake + popup shell into `<body>` at DOM-ready. Then it **relocates** the existing Contact Form 7 form `#wpcf7-f1874-o1` into the popup's `.popup-content` div. Toggle visibility via the `.popup-hidden` class (intentionally namespaced to avoid clashes with WordPress's generic `.hidden` utility).

`document.addEventListener('wpcf7mailsent', …)` listens for the native CF7 success event, checks `event.detail.contactFormId === '1874'`, and auto-closes after 3 seconds.

Capability: `form-frontend`.

### Decision Log

👤 **For the bakery team:**

We chose **Contact Form 7** as the form engine so you can edit the form fields, the destination email address, and the success message **yourself** through the CF7 admin — without ever calling a developer. The cupcake button and the popup wrapper are custom (they have to match the brand), but the form inside is fully managed by you.

🛠 **For developers:**

- **Historical:** the original popup (commit `945f21e`, May 5, 2026) submitted a hardcoded form to a proprietary daridev mail API with `api_key`, `user`, `subject`, `redirect` hidden fields. This was migrated to CF7 in commit `f5a899f` (May 6, 2026). The original integration is gone; CF7 is the only live path.
- **DOM relocation, not form rebuild.** We never construct the form's `<input>`s in JS. We let CF7 render its own form in the DOM, then move it. This means CF7's native validation, error messages, AJAX submission, and `wpcf7mailsent` event all "just work" inside the popup.
- **Anti-flash CSS** hides both `#wpcf7-f1874-o1` (the source CF7 wrapper) and `#custom-popup-wrapper` initially; the wrapper is revealed only after the JS finishes moving the form, preventing a flicker.

### Maintenance Guide

👤 **For the bakery team:**

**To edit the form fields:** WP admin → **Contact** → **Contact Forms** → open the form with shortcode `[contact-form-7 id="1874" …]`. The "Form" tab is where you can add/remove fields. Click **Save** when you're done; the popup picks up your changes immediately.

**To change the destination email:** Same form, **Mail** tab. The "To:" field is the address that receives every submission.

**To change the success / failure messages:** Same form, **Messages** tab.

**Do NOT change the form's ID.** The auto-close behaviour and the form-relocation script both depend on `1874`. If for any reason you need to recreate the form, tell your developer first so they can update the constant in two places (the JS and the CSS).

🛠 **For developers:**

To attach the popup treatment to a different CF7 form (e.g., for a different page), you have to update **three** load-bearing references:
1. The CSS selector in `src/features/popup-form/custom-popup-form.css` (`#wpcf7-f1874-o1`).
2. The JS DOM lookup in `src/features/popup-form/custom-popup-form.js` (`document.querySelector('#wpcf7-f1874-o1 .wpcf7-form')`).
3. The auto-close guard in the same JS file (`event.detail.contactFormId === '1874'`).

---

## Feature group — Venue Wedding Cake Form & Footer Form

The polished long-form order request page at `/order-wedding-cake/` (page id 1122), and the matching newsletter signup styling in the footer.

### Human-Language README

👤 **For the bakery team:**

The page at **`/order-wedding-cake/`** has a long order form with fields like guest count, venue date, your contact details, and a file upload for inspiration photos. We restyled it from a blocky default form ("doctor's office intake sheet") into a polished two-column layout with comfortable spacing, rounded inputs, a soft drop-shadow card, and a custom dashed-border upload zone. On phones it stacks into one column.

The form also includes a **picture-CAPTCHA** (small icons you click to prove you're human). We restyled those too — they're now circular icons that match the brand instead of plain default radio buttons.

In the **site footer**, the newsletter signup form uses the same polished input/button styles for visual consistency.

🛠 **For developers:**

All styles in `src/core/forms/form-style.css` and `src/core/forms/footer-form-style.css`. The wedding-cake form selectors are scoped to `.page-id-1122` (so the styling doesn't leak to any other CF7 form). The footer form rules target `#sns_footer .wpb_raw_html form` plus `#my-proprietary-form` (a legacy id retained for selector reuse from the original daridev mail-API era).

The custom file-upload zone uses `input[type="file"]::file-selector-button` for the inner button, which is a modern CSS pseudo-element supported in all current browsers.

### Decision Log

👤 **For the bakery team:**

The brief was literally **"this looks like a doctor's office intake sheet."** We rebuilt the visuals to feel like a high-end bakery — warm whites, soft shadows, generous spacing, a clearly visible upload area, a CAPTCHA that doesn't look like spam protection from 2008. Everything is still served by Contact Form 7 underneath, so the form's fields and behaviour are unchanged — only the skin.

🛠 **For developers:**

- **Selector reuse with the footer form.** The CSS shares the joint selector list `.page-id-1122 .wpcf7-form … , #my-proprietary-form …` so a single rule paints both surfaces. The `#my-proprietary-form` id is leftover from the original popup form that submitted to the daridev mail API; we kept the id on the footer form specifically to reuse the styling.
- **Date-input padding override.** `input[type="date"]` gets a separate `padding: 0 15px` rule to compensate for the native browser padding the date control adds inside the field.

### Maintenance Guide

👤 **For the bakery team:**

**To edit the wedding-cake form fields:** WP admin → **Contact** → **Contact Forms** → find the form bound to page 1122. Edit fields in the **Form** tab. Save.

**To change who receives submissions:** Same form → **Mail** tab → **To:**.

**Don't change the page slug or the page ID.** All the polished styling is scoped to `.page-id-1122`. If the page is rebuilt with a new ID, the styling disappears.

🛠 **For developers:**

To migrate this form to a different page ID, do a project-wide find-and-replace of `.page-id-1122` to the new ID inside `src/core/forms/form-style.css`. There's no JS to update.

---

## Feature group — Order-Wedding-Cake Colour Switch

The dynamic White ↔ Ivory toggle at the top of the `/order-wedding-cake/` page that swaps every cake image on the page in real time.

### Human-Language README

👤 **For the bakery team:**

At the very top of the wedding-cake order form is a pair of checkboxes labelled **White** and **Ivory**. Tick one and **every cake photo on the page instantly switches to the matching colour version**. They behave like radio buttons — picking one un-picks the other. The page opens with **White checked by default**.

The trick is in the file names: for each cake we have two near-identical photos, one called "...White..." and one called "...Ivory...". When you tick a different colour, the script rewrites the image URLs on the fly. **This is invisible magic — but it depends on you uploading the two photos with matching names.**

🛠 **For developers:**

`src/features/order-cake/order-wedding-cake-change-cake-color.js` detects `form[action^="/order-wedding-cake/"]`. If found, it:

1. Selects all `.owl-stage-outer img, .product-image img` as the swap target set.
2. Wires the White and Ivory checkboxes to exclusive-toggle behaviour (one checked = other unchecked).
3. On change, it walks every target image and rewrites `src` + `srcset` using `replace(/Ivory/g, 'White')` (or vice versa), plus a `replace(/-\d+x\d+(?=\.(jpg|jpeg|png|webp|gif))/gi, '')` to strip dimension suffixes and fall back to the full-resolution original.
4. Carries an exception map for filenames that don't follow the convention (see below).
5. Calls `whiteCheckbox.dispatchEvent(new Event('change'))` on load to apply White as default.

### Decision Log

👤 **For the bakery team:**

We placed the colour selector **at the very top** of the form because the colour choice is the first decision a customer makes about their wedding cake — every photo they see afterwards should match the cake they're actually considering. Putting the selector at the bottom would have meant scrolling back up to change it; putting it inline with each cake would have multiplied the choice unnecessarily.

🛠 **For developers:**

- **Strip-dimensions-first.** WordPress generates `-NNNxNNN` size variants for every uploaded image. The script strips those suffixes (`-600x600` → ``) and serves the original full-resolution file. This avoids broken-link 404s when a particular size variant doesn't exist for one of the two colour versions.
- **Exception map.** Four cakes break the naming convention for historical reasons and require explicit `replace` rules (see Maintenance Guide below). These exceptions are **frozen** — they will not be expanded.
- **Replace-order matters** for the Pindots cake: `WPindotsWhite` must be matched before `WPindots` to avoid the first replace eating the second.

### Maintenance Guide — image-upload conventions

**This is the highest-stakes operator instruction in this document. Read carefully.**

👤 **For the bakery team:**

For the colour swap to work on a new wedding cake, you must upload **two photos**:

1. **Same filename**, with only the colour word swapped:
   - ✅ `lovely-cake-white.jpg` + `lovely-cake-ivory.jpg`
   - ❌ `lovely-cake-white.jpg` + `lovely-cake-cream.jpg` (wrong colour token)
   - ❌ `lovely-cake-white.jpg` + `cake-i-took.jpg` (wrong filename)
2. **Same file extension** — both `.jpg`, or both `.png`. Not one of each.
   - ✅ `pretty-cake-white.png` + `pretty-cake-ivory.png`
   - ❌ `pretty-cake-white.png` + `pretty-cake-ivory.jpg`
3. **Plain white background**, like every other cake on the site, so the photo matches the gallery aesthetic when shown later.
4. **Spell the colour words exactly as `White` and `Ivory`** (capital W and capital I — the script is case-sensitive).

If you follow these four rules, the colour swap will work automatically with no developer involvement.

**Legacy frozen exceptions.** Four older cakes don't follow the convention and have hand-written exceptions in the script. **This list is frozen — it will not be expanded for any new cake.** If you want a new cake to support the colour swap, follow the convention above. If a cake's filenames cannot be changed to match, the colour swap will silently skip that cake.

The four frozen exceptions are:

| Cake | Ivory filename | White filename | Note |
| --- | --- | --- | --- |
| Rustic / White Stucco | `5RusticStuccoIvoryWeddingCake` | `5WhiteStuccoIvoryWeddingCake` | Both names contain "Ivory" — special replace pattern. |
| Pindots | `3PindotsIvoryWeddingCake.jpg` | `WPindots.png` (or `WPindotsWhite.png`) | Different filename **and** different extension. |
| Screen-Shot | `Screen-Shot-2022-07-11-at-10.58.40-PM` | `Screen-Shot-2022-07-11-at-10.58.32-PM` | Only the timestamp differs, no colour token. |
| Exquisite (permanent-white) | n/a — always shown in white | `11ExquisiteWhiteWeddingCake` | Has no ivory variant; the script is hardcoded to leave it alone. |

🛠 **For developers:**

The exception map lives at `src/features/order-cake/order-wedding-cake-change-cake-color.js` lines 27–73. **Do not extend it.** New exceptions accumulate as bugs; the policy decision is that new cakes must follow the convention. If a new cake genuinely cannot be renamed (e.g., because of external dependencies), that's a candidate for a separately-scoped follow-up rather than a quiet exception-map addition.

If you need to find a particular cake's image filenames quickly:

```bash
ls wp-content/uploads/**/* | grep -i "wedding-cake"
```

---

## Feature group — Branding & Layout

The site-wide visual decisions: logo handling, the cake-icon header swap, hidden chrome on gallery pages.

### Human-Language README

👤 **For the bakery team:**

**Your "P" logo** appears at full height in the header on every page — including phones, where the default WordPress theme would have squashed it. The little **cake icon in the top-right header** is the favourites shortcut; the red number badge next to it shows how many cakes a visitor has saved. Tap it to jump to the favourites board.

On the gallery and favourites pages we **hide a lot of WordPress chrome** — the sidebar, breadcrumbs ("Home > Shop > …"), the search widget, the page title, and the toolbar at the top of the shop. This keeps the focus entirely on the cake photos.

🛠 **For developers:**

`src/core/branding/logo-styles.css` has a single mobile rule that removes the SNS Vicky theme's mobile `max-height` cap from the logo. The cake icon swap is in `src/features/favorites/header-fav.css` — it overrides the `.mini-wishlist .tongle:before` Font Awesome glyph to `\f1fd` (`fa-birthday-cake`) and restyles the count badge.

`src/core/layout/layout-and-hidden-elements.css` hides everything we want gone on the shop/category/tag pages: `#sns_breadcrumbs`, `#woocommerce_recently_viewed_products-4`, `#woocommerce_product_search-2`, `.sns-woocommerce-page .page-title`, `.term-description`, `.toolbar-top`. `src/features/gallery/product-gallery.css` hides the per-card chrome (`.item-content`, `.item-box-hover`, `.product-shop`, the header `leftsidebar` icon).

### Decision Log

👤 **For the bakery team:**

The bakery is **about the cakes** — not breadcrumbs, not sidebars full of widgets, not search boxes. Every piece of chrome we removed was a piece of visual noise competing with the photos. We swapped the default WordPress wishlist heart-icon in the header for a **birthday-cake icon** because the brand is, you know, a bakery.

🛠 **For developers:**

- **`header-style4` is the only supported layout.** Every CSS selector that targets the header assumes that body class. If the WordPress Customizer is used to switch to a different SNS Vicky header style, the logo regression rule, the header cake icon, and the "leftsidebar" icon-hide rule will all silently stop applying.
- **Theme integration boundary.** We never edit theme template files. Every customisation rides on top via CSS specificity (often using `!important`). This means major theme updates can wipe through and the customisations stay intact — but it also means we depend on theme-internal class names (`.sns-left`, `.sns-main`, `.block-product-inner`, `.product-inner`, `.item-img-info`) staying stable across theme updates.

### Maintenance Guide

👤 **For the bakery team:**

**The "P" logo regresses to a tiny version on mobile.** This means either (a) the SNS Vicky theme updated and reintroduced its mobile `max-height` rule, or (b) someone changed the header style in the Customizer. Solution: check Customizer → Header → make sure "Style 4" is selected. If that's correct, contact your developer — the theme update likely overrode our CSS.

**The cake icon in the header is back to a generic heart.** Same root causes as above. Tell your developer.

🛠 **For developers:**

The cake-icon override depends on Font Awesome being loaded — which the SNS Vicky theme enqueues globally, but also we re-import a newer version from cdnjs in `src/assets/icons/import-font-awesome-icons.html`. If Font Awesome ever stops loading, the icon falls back to whatever character is at codepoint `\f1fd` in the default font (usually a blank square).

If a new header style is genuinely needed, every CSS rule scoped to `body.header-style4` will need to be reviewed. Search the codebase for `header-style4` to find the affected files.

---

## Operational maintenance guide (WP Engine specifics)

This site lives on WP Engine. There are three things WP Engine does **differently** from a standard WordPress install that you need to know.

### How to edit the live site safely

👤 **For the bakery team:**

You don't normally need to edit code on the live site — but if you ever do, **do not use the built-in "Theme File Editor"** in WP admin. On this WP Engine install, it writes to a hidden shadow path and your changes won't actually go live. Use the **WP File Manager** plugin instead (it's already installed). Navigate to:

```
/wp-content/themes/snsvicky/functions.php
```

Edit there, save, and your changes are live.

After editing PHP, ask your developer to **invalidate OPcache** — WP Engine's "Clear All Caches" button does not always do this. If they're unavailable, an admin-only fallback URL exists: `https://palermobakery.com/?palermo_reset_opcache=1` (only works while logged in as an admin).

🛠 **For developers:**

- WP Engine's Theme File Editor writes to a deployment-shadowed path that doesn't affect the running site. Always use WP File Manager (plugin), or SFTP via WP Engine's user portal, paired with a manual `opcache_invalidate()` call.
- OPcache is **not** invalidated by SFTP file changes. The cleanest paths are: (a) WP Engine portal → "Reset PHP" (most reliable), (b) the admin-only `?palermo_reset_opcache=1` URL snippet (add the snippet to `functions.php` if not already present), (c) Theme File Editor *would* invalidate via `opcache_invalidate()` on save, but it writes to the wrong path on this install.

### How to test changes

👤 **For the bakery team:**

Open the site in a **Private / Incognito** browser window. This bypasses both WP Engine's edge cache and LiteSpeed's browser cache, so you see what a fresh visitor would see. Always test on a representative device matrix:

- 📱 **Mobile phone** (iPhone Safari especially — the original iOS freeze surfaced there)
- 📱 **Tablet**
- 💻 **Laptop**
- 🖥 **Desktop PC**

The gallery and the lightbox were specifically optimised for iPhone Safari; if you're verifying a fix related to gallery performance, an iPhone test is non-negotiable.

🛠 **For developers:**

- Private/Incognito bypasses LiteSpeed's browser-level cache.
- For server-edge bypass, the WP Engine portal also has a "Purge All Caches" button.
- For CSS/JS file-level cache busting during dev, append a query string (e.g., `?v=2`) to the file URL inside the Simple Custom CSS and JS plugin entry — every save changes the inline hash anyway, so this is rarely needed.

### Before major updates

👤 **For the bakery team:**

Before running any major WordPress or WooCommerce update (anything that goes from version X to version X+1, e.g., WP 6.x → 7.0, WC 8.x → 9.0):

1. **Run a WPvivid Backup first.** WP admin → WPvivid Backup → "Backup Now". Wait for confirmation.
2. **Verify the backup was saved** (WPvivid shows a green tick).
3. **Then** run the update.

After the update, **test in incognito** across the device matrix. Pay particular attention to:

- Hearts appearing on cake cards in the gallery (custom JS-injected, can be broken by theme updates).
- The lightbox opening when you tap a cake (depends on prettyPhoto bindings, can be broken by WC updates).
- The "Ask Me" cupcake popup still relocates and submits the right form (depends on CF7 form id 1874 — make sure nothing renumbered it).

🛠 **For developers:**

Fragile areas that often break on major updates:
- `prettyPhoto` is bundled inside WooCommerce — a WC major release could remove it. Detect via `$.fn.prettyPhoto` check in `image-lightbox.js`.
- YITH Wishlist updates occasionally rename `data-fragment-ref` to something else. This attribute is now consumed by **two** features: the heart button injection on gallery cards (`fav-button.js`) and the lightbox title permalink resolution (`fav-button.js` via the PHP-emitted `data-product-permalink`). A YITH rename breaks both at once. If hearts on cards stop registering clicks, this is the first place to look.
- SNS Vicky theme updates can re-introduce the mobile logo `max-height` cap.
- Simple Custom CSS and JS updates can reorder script execution; we use `if (window.foo) return;` guards in every JS file, so this should fail safe but is worth verifying.

### When to call us back

👤 **For the bakery team:**

Contact your developer if any of these symptoms appear and clearing the cache + incognito testing doesn't fix them:

- The gallery looks broken (cards have no images, layout is hot pink, the page freezes on iPhones).
- The lightbox stops opening when you tap a cake.
- Favourites stop syncing across devices for logged-in users.
- A share link opens an empty page or shows the wrong cake.
- The colour switcher on the wedding-cake page doesn't change one of the cakes (and you've already verified the filename convention).

Send a **screenshot or short video from your phone** showing the issue. That speeds up diagnosis enormously.

---

## Future enhancements (not yet delivered)

**Nothing in this section is delivered today.** Each item requires a separate scope. Use this list as a menu for follow-up work — items here have been discussed in past project conversations and the scope is already partly understood, so they're cheaper to revisit than to design from scratch.

### Email-channel sharing for boards
**Discussed scope:** the current share buttons copy a link to the clipboard. A future enhancement would add a built-in "send via email" option — either a `mailto:` button next to the existing share button, or a `navigator.share()` invocation on mobile (which surfaces the native phone share menu with email, WhatsApp, Messages, etc.). No third-party email service involvement.

### Consultant address book
**Discussed scope:** for the bakery's internal wedding-cake consultants, a small WP-admin-managed list of email addresses (one per consultant). Sharing a cake or a board could then offer "Send to a consultant" with a dropdown, pre-filling the recipient. Lives entirely within WP admin; no external CRM.

### ESP / marketing-list automation
**Discussed scope:** today the bakery exports the WP user list manually to feed a newsletter tool. A future enhancement would automate this — connect WordPress user registration directly to a chosen email-marketing service so new sign-ups land in a newsletter list without manual export. Tool choice (MailerLite, Mailchimp, Klaviyo, etc.) deliberately left open for the future engagement.

### Multi-language support (i18n)
**Discussed scope:** today every UI string ("My Favorite Cakes", "Cakes Shared With You", "Link Copied!", "Ask Me", "Login", "Sign Up", "Loading your favorite cakes…") is hardcoded in English. Adding multi-language support would extract these into a translation table, integrate WPML or Polylang for the page-content layer, and verify all custom CSS/JS still works under right-to-left languages.

### Third colour option on the wedding-cake switcher
**Discussed scope:** today the switcher offers White and Ivory. A third option (the previously mentioned candidate was a warm cream / off-white) would require: another checkbox in the form (CF7 side), an extension to the swap logic in the JS file (a third colour token to match against filenames), and a new naming convention for the third variant. All four frozen exceptions would also need their third-variant filename mapped if they're to support it.

### Background-removal local script + tutorial (optional extra)
**Discussed scope:** a separate "one-time tool" deliverable, not bundled with the website. A small script the bakery runs locally on their own computer to batch-process cake photos and produce versions with the background removed (or replaced with plain white). Includes a short written tutorial explaining how to install and run it. Not a recurring service — a one-off deliverable.

---

*Document maintained at `docs/client-readme.md`. The binding technical contract for every feature lives in `openspec/specs/`; this document is the human narrative companion to those specs. Last updated: 2026-05-17.*
