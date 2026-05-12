# Proposal: Fix Gallery Thumbnail Race Condition and Performance

## Why

The gallery optimization from `optimize-gallery-images-resolution` cannot prevent the browser from loading full-resolution images because the `<img src="...">` in the initial HTML already contains the full-res URL. Browsers start loading images **during HTML parsing**, 4-5 seconds before any JavaScript runs. On iOS Safari, 300+ simultaneous full-res requests saturate the connection pool (6 connections per domain), causing the page to time out or crash. 4929 failed image requests were observed in testing (403/ERR_ABORTED). The lightbox also fails because its full-res request never gets through.

## What Changes

- Add a WordPress PHP filter (`wp_get_attachment_image_attributes`) that rewrites `<img src="...">` from full-resolution to `-300x300` thumbnail in the server-generated HTML — the browser never sees the full-res URL
- Add a `woocommerce_get_image_size_shop_catalog` filter to ensure the catalog image size is 300x300
- Simplify `image-lightbox.js` back to its core responsibilities: sync data attributes, bind prettyPhoto, watch for new items via MutationObserver — the complex IntersectionObserver/MutationObserver guards were removed since the PHP filter addresses the root cause

## Impact

- **Affected spec**: gallery-optimization
- **Affected files**: `fix-gallery-thumbnail-src.php` (NEW), `image-lightbox.js` (simplified)
- **Performance**: Initial HTML contains only `-300x300.jpg` URLs — the browser loads small thumbnails, not 3MB full-res images
- **User Experience**: Gallery loads in seconds on all devices; lightbox works because connections are free for the single full-res request
