## Why

The Favorite Cakes page currently displays images using the WordPress 'large' size, which appends dimension suffixes (e.g., `-1024x1024`) to the filenames. This can result in blurred images if the 'large' size is smaller than the display area, and it creates a dependency on specific WordPress thumbnail generation settings. Switching to the original 'full' size ensures maximum image quality and consistency across all favorites.

## What Changes

- Modify the `ajax_render_favorite_products` PHP function to request the `full` image size instead of `large`.
- Update the favorites grid rendering to ensure all image URLs (both in the grid and the lightbox links) use the un-suffixed original file.

## Capabilities

### New Capabilities
- `favorites-full-size-images`: Ensures the favorites grid and its associated lightbox use original, full-sized images without dimension suffixes.

### Modified Capabilities
- `favorites-lightbox`: Ensure the lightbox binding in the favorites grid correctly handles the full-resolution image URLs.

## Impact

- `functions.php`: The `ajax_render_favorite_products` function will be updated.
- `function.php`: The duplicate `ajax_render_favorite_products` function (if active) will be updated for consistency.
- Favorite Cakes page UI and lightbox.
