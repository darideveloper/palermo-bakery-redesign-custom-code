# Design: Image Resolution Optimization

## Architectural Overview
The optimization logic will be integrated into the existing `image-lightbox.js` script. It will leverage the `initCakeGallery` function, which is already responsible for preparing images for the lightbox.

## Key Components

### 1. Source Transformation Logic (Refined)
A utility logic will be used to transform an image URL into its 300x300 thumbnail counterpart.
- **Problem**: Large images have `-scaled.jpg`. Thumbnails are usually generated from the original name (e.g., `image-300x300.jpg`).
- **Logic**:
  1. Identify the high-res URL.
  2. Strip `-scaled` if present.
  3. Append `-300x300` before the extension.
- **Attribute Target**:
  - `src`: For immediate preview.
  - `data-original`: To prevent Lazy Load from reverting the change.
  - `srcset`: Removed (to prevent the browser from automatically picking a higher resolution based on screen density).

### 2. Gallery Initialization Hook
Inside `initCakeGallery`, the script will:
1. Identify the original high-resolution source from `data-original` or `src`.
2. Save the *true* high-res link to the parent anchor's `href`.
3. Apply the transformation to create the optimized thumbnail URL.
4. Update both `src` AND `data-original` (or `data-src`) attributes.
5. Remove the `srcset` attribute if it exists.

### 3. State Management & Safety
- **jQuery Wrapper**: Use `jQuery(function($) { ... })` or explicit `jQuery` calls to avoid `$ is not a function` errors.
- **Double-Processing**: Continue using `.gallery-ready` and `.link-ready` classes.
- **404 Prevention**: The script will check if the URL already contains `-300x300` before applying the transform.

## Technical Considerations
- **AJAX Compatibility**: Hooked into `initCakeGallery` which runs on refresh.
- **Performance**: Reducing the memory footprint of images on mobile is the primary goal to prevent iOS crashes.
- **Regex Robustness**: Handles extensions and strips '-scaled'.

### 4. Lazy Loader Conflict Resolution
The theme's `jquery.lazyload.js` was reverting `src` back to the high-res URL immediately after the script set it.
- **Root Cause**: The `lazy` class remained on images, so the lazy loader kept managing them. When the script updated `src`, the lazy loader intercepted the change and "fixed" it back to the original high-res URL (read from `data-original` or an internal cache).
- **Fix**: Remove the `lazy` class from images using `$img.removeClass("lazy")`.
- **Ordering**: The class removal must happen BEFORE the `src`/attribute changes. This prevents any MutationObserver-based lazy loader from reacting to the attribute changes.
- **Previous attempt**: Adding `loaded` class was insufficient — it attempted to trick the lazy loader but didn't remove the trigger (`lazy` class), so the loader continued managing the image.

## Alternatives Considered
- **Native srcset**: Hard to implement without server-side changes to the theme's core logic.
- **CSS `object-fit`**: Doesn't reduce download size, only visual fit.
