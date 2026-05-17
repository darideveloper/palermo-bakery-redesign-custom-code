## Context

The Favorite Cakes page utilizes an AJAX-driven grid to display saved products. The PHP backend currently requests the `large` image size via `wp_get_attachment_image_url()`. This results in URLs like `...-1024x1024.jpg`, which the user wants replaced with the original `....jpg` files to ensure maximum quality and avoid dimension suffixes.

## Goals / Non-Goals

**Goals:**
- Ensure the favorites grid displays full-resolution images without dimension suffixes.
- Implement the fix server-side for maximum efficiency and reliability.
- Ensure consistency between the grid display and the lightbox view.

**Non-Goals:**
- Changing image sizes in the main WooCommerce gallery (which already uses thumbnails for performance).
- Implementing a client-side JavaScript cleanup (though considered, it is less efficient than fixing the PHP source).

## Decisions

1. **Server-side fix in AJAX handlers**:
   - **Decision**: Update `wp_get_attachment_image_url(..., 'large')` to `wp_get_attachment_image_url(..., 'full')`.
   - **Rationale**: It is cleaner and more performant to generate the correct URL at the source than to have the browser parse and replace strings in the DOM after every AJAX load.
   - **Alternative Considered**: JavaScript regex replacement in `fav-button.js` (`/-(\d+)x(\d+)\.(jpg|jpeg|png|webp|gif)/gi`).

2. **Synchronize `functions.php` and `function.php`**:
   - **Decision**: Update the `ajax_render_favorite_products` function in both files.
   - **Rationale**: The codebase contains duplicate versions of this logic. Ensuring parity prevents regressions if the system's loading priority changes.

## Risks / Trade-offs

- **[Risk] Increased Page Weight** → Full-size images might be significantly larger than the 1024px versions.
  - **Mitigation**: The favorites grid is personalized and usually contains a subset of products. The visual quality improvement for a "Favorites" showcase justifies the trade-off.
- **[Risk] Caching** → Old URLs might be cached by the browser or WP Engine.
  - **Mitigation**: Hard-reload and cache clearing are included in the verification tasks.
