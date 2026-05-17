## 1. Backend PHP Updates

- [x] 1.1 Update `ajax_render_favorite_products` in `functions.php`: Change `wp_get_attachment_image_url(..., 'large')` to `wp_get_attachment_image_url(..., 'full')`.
- [x] 1.2 Update `ajax_render_favorite_products` in `function.php`: Change `wp_get_attachment_image_url(..., 'large')` to `wp_get_attachment_image_url(..., 'full')`.
- [x] 1.3 Verify both files are synchronized and correctly handle the 'full' image size request.

## 2. Frontend Validation

- [ ] 2.1 Load the `/favorite-cakes` page and inspect the rendered grid.
- [ ] 2.2 Confirm that `<img>` tags in the masonry grid use URLs without dimension suffixes (e.g., `-1024x1024`).
- [ ] 2.3 Confirm that `<a>` tags in the masonry grid have `href` values pointing to the original, un-suffixed images.

## 3. Lightbox Verification

- [ ] 3.1 Click a product card on the favorites page to open the prettyPhoto lightbox.
- [ ] 3.2 Inspect the image inside the lightbox to ensure it is the full-resolution original file.
- [ ] 3.3 Verify that navigation arrows within the lightbox correctly transition between full-size images.

## 4. Cache and Cleanup

- [ ] 4.1 Clear any relevant WordPress or server-level caches (WP Engine).
- [ ] 4.2 Perform a hard reload (Ctrl+F5) to ensure the latest PHP output is rendered in the browser.
