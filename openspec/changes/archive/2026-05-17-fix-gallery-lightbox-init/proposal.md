## Why

The gallery lightbox currently fails to initialize for images loaded via infinite scroll and sometimes fails for the initial batch due to a race condition in the chunked loading logic. This results in users clicking images and being taken to a raw image file URL instead of opening the professional prettyPhoto lightbox.

## What Changes

- Refactor `image-lightbox.js` to support incremental initialization of the lightbox.
- Remove the `prettyPhotoInitialized` guard that prevents the lightbox from binding to newly added content.
- Implement a targeted binding strategy that ensures `prettyPhoto` is only attached to elements that have not yet been initialized, preventing double-binding.
- Synchronize the lightbox initialization with the chunked "card preparation" logic to ensure images are ready for the lightbox as soon as they are visible.

## Capabilities

### New Capabilities
- None.

### Modified Capabilities
- `gallery-optimization`: Update the initialization requirements to support dynamic/incremental binding without breaking existing performance and iOS stability fixes.

## Impact

- `image-lightbox.js`: Primary logic for gallery initialization and lightbox binding.
- Gallery UX: Improved reliability of the lightbox across all images, including those loaded via infinite scroll.
