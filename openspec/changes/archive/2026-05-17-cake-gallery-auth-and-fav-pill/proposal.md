## Why

The cake gallery page currently lacks a clear entry point for unauthenticated users to log in or register, and there is no quick shortcut for logged-in users to navigate to their saved favorites from the gallery filter row. Both gaps reduce user engagement and discoverability of the favorites feature.

## What Changes

- A **Login / Sign Up button pair** is injected below the category filter widget on the cake gallery page. The buttons redirect to `/login` and `/register` respectively and are **hidden when the user is already logged in**.
- A **"Favorite Cakes" pill** is added as the first item in the existing WooCommerce category filter row. It displays a heart icon, matches the existing pill styling, and redirects to `/favorite-cakes` instead of filtering the product grid.

## Capabilities

### New Capabilities

- `gallery-auth-buttons`: Login and Sign Up call-to-action buttons rendered below the gallery filter row, visible only to guests (logged-out users).
- `gallery-fav-pill`: A "Favorite Cakes" redirect pill injected as the first item in the WooCommerce category filter row, with a heart icon and consistent pill styling.

### Modified Capabilities

<!-- No existing spec-level behavior is changing -->

## Impact

- **`functions.php`** — New PHP hook (`woocommerce_before_shop_loop`) outputs the auth buttons HTML; `is_user_logged_in()` controls server-side visibility.
- **`category-loader.js`** — Move the `.gallery-auth-buttons` out of the theme's hidden toolbar and place them below the visible `#woocommerce_product_categories-3` widget. Append the Favorite Cakes `<li>` as the first child of `#woocommerce_product_categories-3 ul.product-categories` on DOM ready; also triggers the category-loader spinner on pill click (consistent with existing behavior).
- **`category-filter-menu.css`** — New styles for `.gallery-auth-buttons` block (button pair) and `.fav-pill-link` modifier on the injected pill.
- No new dependencies, no breaking changes, no API changes.
