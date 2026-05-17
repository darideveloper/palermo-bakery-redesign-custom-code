## Context

The current `image-lightbox.js` implementation was designed to protect iOS Safari from crashing by processing product cards in small chunks (30 at a time) and using native lazy loading. However, it uses a global `prettyPhotoInitialized` flag that prevents the lightbox from being set up more than once. This causes two issues:
1. Images in chunks 2 through N are "prepared" (given their lightbox attributes) *after* the initialization has already locked itself.
2. New images appended by infinite scroll never have the lightbox handler attached.

## Goals / Non-Goals

**Goals:**
- Ensure the `prettyPhoto` lightbox works for every image in the gallery, regardless of when it was loaded or processed.
- Preserve the iOS Safari performance optimizations (chunking, native lazy loading, no animated GIF decoding).
- Prevent "double-binding" where clicking an image might trigger multiple lightbox overlays.

**Non-Goals:**
- Replacing the `prettyPhoto` library.
- Modifying the visual style or layout of the gallery.

## Decisions

### 1. Shift from Global to Incremental Initialization
We will remove the `prettyPhotoInitialized` boolean flag. Instead, the initialization logic will be designed to run multiple times safely.

### 2. Use a "Bound" Marker to Prevent Double-Binding
To ensure we don't attach multiple click listeners to the same anchor tag, we will introduce a marker class (e.g., `pp-bound`).
- **Rationale:** `prettyPhoto` attaches event listeners to the selected elements. Calling it twice on the same element can lead to bugged behavior. By selecting only `a[data-rel^='prettyPhoto']:not(.pp-bound)`, we ensure idempotency.

### 3. Integrated Initialization Trigger
The lightbox initialization will be called:
- At the end of each chunk processing step in `processCardsChunked`.
- When new elements are detected via infinite scroll hooks.
- **Rationale:** This ensures that as soon as a batch of 30 images is "prepared" (meaning their `href` and `data-rel` attributes are correctly set), they are immediately made interactive.

### 4. Scoped Selectors
When possible, the initialization will be scoped to the specific elements just added or processed to minimize DOM traversal overhead.

## Risks / Trade-offs

- **[Risk]** Memory usage on iOS if too many event listeners are attached.
  - **[Mitigation]** We are only attaching listeners to the `<a>` tags. 300-500 listeners is trivial compared to the image decoding overhead we already solved.
- **[Risk]** Race condition where an image is clicked before its chunk is processed.
  - **[Mitigation]** The first 30 images (viewport) are processed synchronously. Sub-second delays for images further down the page are acceptable and expected for performance.
