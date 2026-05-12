# Proposal: Optimize Gallery Image Resolution

## Why
The current gallery loads full-resolution images for the grid preview. This causes significant performance issues on mobile devices (especially iOS), leading to browser crashes and slow page loads.

## What Changes
Implement an automatic image resolution switcher that:
1. Uses WooCommerce-generated 300x300 thumbnails for the grid preview.
2. Maintains high-resolution images for the prettyPhoto lightbox.

This will be achieved by intercepting the image `src` in `image-lightbox.js` and transforming it to include the `-300x300` suffix for the preview, while preserving the original source for the lightbox `href`.

## Goals
- Improve mobile performance and stability (prevent iOS crashes).
- Reduce initial page load time and data usage.
- Ensure the lightbox still displays high-quality images.

## Impact
- **Frontend**: Faster rendering of the product grid.
- **User Experience**: Smoother scrolling and interaction on mobile.
- **Infrastructure**: Reduced bandwidth consumption.
