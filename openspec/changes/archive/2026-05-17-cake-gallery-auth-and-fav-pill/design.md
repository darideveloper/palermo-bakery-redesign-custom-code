## Context

The cake gallery is a WooCommerce shop archive page (`is_shop()` / `is_product_category()`). A helper `_palermo_is_gallery_view()` already exists in `functions.php` to identify these pages. The category filter pills are rendered by the WordPress widget `#woocommerce_product_categories-3`. Custom code is injected via this repository's files (CSS, JS, PHP snippets loaded by the theme/plugin).

Key existing infrastructure:
- `cakeFavsData.isLoggedIn` is already injected into every page via `inject_cake_favs_data()` in `functions.php`, so JS can read login state without an extra AJAX call.
- `category-loader.js` already targets `#woocommerce_product_categories-3 ul.product-categories li a` for click handling.
- `category-filter-menu.css` owns all pill button styling.

## Goals / Non-Goals

**Goals:**
- Render Login and Sign Up buttons immediately below the filter widget for guest users only.
- Inject a "♥ Favorite Cakes" pill as the first item in the WooCommerce category filter row.
- Match the existing site visual style throughout (pill shape, border, hover states).
- Use server-side `is_user_logged_in()` for auth-button visibility — no client-side flash.

**Non-Goals:**
- Changing the `/login`, `/register`, or `/favorite-cakes` page themselves.
- Implementing any authentication logic.
- Modifying the WooCommerce widget or theme template files.
- Adding the auth buttons or fav pill to any page other than the gallery.

## Decisions

### Decision 1: PHP hook for auth buttons (not JS injection)

**Chosen**: Hook into `woocommerce_before_shop_loop` in `functions.php` and gate with `is_user_logged_in()`.

**Rationale**: Server-side rendering eliminates the FOUC (flash of unwanted content) that would occur if JS hid the buttons after paint. It is also simpler and more reliable — no JS dependency.

**Alternative considered**: Inject via jQuery after DOM ready, using `cakeFavsData.isLoggedIn`. Rejected because buttons would briefly flash before JS hides them on first paint on slow connections.

### Decision 2: JS injection for Favorite Cakes pill (not PHP)

**Chosen**: Append an `<li>` as `prepend()` into `#woocommerce_product_categories-3 ul.product-categories` inside `category-loader.js`.

**Rationale**: The WooCommerce Product Categories widget does not expose a PHP filter to append custom `<li>` items without overriding the entire widget class. JS injection is the lightest-touch approach and is consistent with how `category-loader.js` already manipulates the same element.

**Alternative considered**: Override the WooCommerce widget via PHP. Rejected — fragile against theme/plugin updates and out of scope for this custom-code repository.

### Decision 3: Style the fav pill with a `.fav-pill-link` class

**Chosen**: Add `.fav-pill-link` class to the injected `<a>` tag. The pill inherits base styles from the existing `ul.product-categories li a` rule; the extra class adds the heart icon via `::before` pseudo-element and provides a hook for any future differentiation.

**Rationale**: Keeps the CSS additive — we never override the existing pill rules, only augment them.

### Decision 4: Auth buttons below the filter widget, above the product grid

**Chosen**: `woocommerce_before_shop_loop` action hook, followed by JS DOM manipulation in `category-loader.js`.

**Rationale**: The hook renders the HTML server-side to prevent guest FOUC (Decision 1). However, the specific theme in use (`snsvicky`) wraps the output of this hook in a `.toolbar-top` container that has `display: none` hardcoded for gallery views. Since unhiding the toolbar would expose unwanted sorting dropdowns, the buttons are rendered via PHP, and then a tiny JS snippet in `category-loader.js` moves them out of the hidden container and places them directly below the visible `#woocommerce_product_categories-3` widget.

## Risks / Trade-offs

- **Widget ID hard-coding** (`#woocommerce_product_categories-3`): The numeric suffix is assigned by WordPress and could change if the widget is removed and re-added. → Mitigation: The existing `category-loader.js` and `category-filter-menu.css` already depend on this same ID; risk is pre-existing and accepted.

- **`woocommerce_before_shop_loop` hook position**: Some theme layouts wrap the product loop in an extra container, so "below filters" may not be pixel-perfect without CSS adjustment. → Mitigation: Use `margin-top: -30px` offset or adjust after visual QA.

- **`/login` and `/register` slugs**: These are hardcoded. If the site changes WooCommerce My Account page slugs, the links break. → Mitigation: Use `wc_get_page_permalink('myaccount')` for login if needed in a future iteration; for now the client has confirmed the slugs.
