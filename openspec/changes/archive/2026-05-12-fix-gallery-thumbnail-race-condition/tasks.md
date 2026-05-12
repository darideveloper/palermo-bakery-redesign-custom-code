# Tasks: Fix Gallery Thumbnail Race Condition and Performance

## 1. Implementation

### 1.1 Add PHP filter to serve thumbnail src in initial HTML
- [x] Create `fix-gallery-thumbnail-src.php` with a `wp_get_attachment_image_attributes` filter that rewrites `src` to `-300x300` for product loop images
- [x] Add `woocommerce_get_image_size_shop_catalog` filter to ensure catalog image size is 300x300
- [x] Include a fallback regex replacement for images where the Attachment API returns full-res
- [x] Only target `shop_catalog` class images to avoid affecting other parts of the site <!-- id: 0 -->

### 1.2 Simplify image-lightbox.js
- [x] Remove IntersectionObserver lazy loading (not needed when initial HTML already has thumbnail src)
- [x] Remove MutationObserver src protector (no full-res src in HTML to revert to)
- [x] Remove queue management, processQueue, monitorImageLoad (complexity not justified)
- [x] Remove retryLightboxImage function (PHP src fix prevents connection pool saturation)
- [x] Remove setupThemeGuard / ajaxComplete guard (PHP filter applies to all requests, including AJAX)
- [x] Keep core: data attribute sync, prettyPhoto binding, MutationObserver for new items <!-- id: 1 -->

## 2. Verification

### 2.1 Visual regression tests
- [ ] **Grid thumbnail test**: Verify all gallery images display at thumbnail resolution (`-300x300`) in the grid — check `src` attribute via dev tools
- [ ] **Lightbox test**: Click each gallery image and confirm the lightbox opens with the full-resolution version
- [ ] **Infinite scroll test**: Load more items via scroll and verify both the grid thumbnails and lightbox work for new items
- [ ] **Category filter test**: Switch categories and verify thumbnails are applied correctly to filtered results <!-- id: 2 -->

### 2.2 Performance tests
- [ ] **Network test**: Verify no full-res (`-scaled.jpg`) image requests appear in the Network tab on initial load
- [ ] **Mobile test**: Test on iOS Safari (or Chrome DevTools device emulation) to verify no browser crashes or timeout
- [ ] **Connection test**: Verify total image requests are limited to 300x300 thumbnails only <!-- id: 3 -->

### 2.3 Edge case tests
- [ ] **Image types**: Verify `.jpg`, `.jpeg`, `.png`, `.webp` extensions all produce correct thumbnail URLs
- [ ] **No `-scaled` suffix**: Verify images without the WordPress `-scaled` suffix still get `-300x300` appended correctly
- [ ] **Missing thumbnails**: If a `-300x300` thumbnail doesn't exist on the server, verify the image gracefully degrades (shows original or placeholder) <!-- id: 4 -->
