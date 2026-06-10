## Why

The favorites page (`/favorite-cakes/`) currently shows a plain text "login" link inside the loading message for guest users. This is inconsistent with the main cake gallery, which renders polished pill-style Login and Sign Up buttons. Adding the same button pair to the favorites page creates a consistent guest CTA experience across the site.

## What Changes

- Render Login and Sign Up buttons on the favorites page when the visitor is not logged in
- Buttons appear between the "My Favorite Cakes" title and the loading message area
- Reuse the existing `.gallery-auth-buttons` CSS class so styling is identical to the gallery page
- The existing text-based "Please login" link inside the loading message is preserved (JS left untouched)

## Capabilities

### New Capabilities
- `fav-page-auth-buttons`: Guest users see styled Login and Sign Up buttons on `/favorite-cakes/` when not authenticated. Logged-in users see no buttons.

### Modified Capabilities
- *(none — behavior of existing specs is unchanged)*

## Impact

- **`src/core/functions.php`**: Modify `render_favorite_cakes_page()` shortcode to conditionally output auth buttons
- **CSS**: No changes — `.gallery-auth-buttons` styles in `category-filter-menu.css` already apply globally
