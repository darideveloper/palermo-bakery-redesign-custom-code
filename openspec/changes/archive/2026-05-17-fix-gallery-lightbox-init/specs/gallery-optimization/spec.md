# gallery-optimization Delta Specification

## Modified Requirements

### Requirement: Incremental Lightbox Initialization

The gallery SHALL support incremental initialization of the lightbox. As new product cards are added to the DOM (via infinite scroll or background chunking), the script SHALL bind the `prettyPhoto` lightbox specifically to the newly prepared elements. The script SHALL NOT use a global initialization flag that prevents subsequent bindings for dynamic content. To prevent double-binding (multiple event listeners on the same element), the script SHALL use a selector or marker that excludes elements already initialized.

#### Scenario: Infinite scroll cards are lightbox-ready
- **Given** the initial page load has completed and the lightbox is functional for the first batch of images.
- **When** the user scrolls and new cards are appended to the DOM.
- **Then** `image-lightbox.js` SHALL detect the new cards and bind `prettyPhoto` to their lightbox links.
- **And** clicking a new card SHALL open the lightbox, not the raw image URL.

#### Scenario: Chunked loading ensures lightbox binding
- **Given** a large batch of images is being processed in chunks (e.g., 30 cards per chunk).
- **When** a chunk of cards completes its "preparation" phase (setting `data-rel`, `href`, and `gallery-ready` status).
- **Then** the lightbox binder SHALL be triggered for those specific cards.
- **And** the binder SHALL NOT interfere with or re-bind cards from previous chunks.

#### Scenario: Performance and iOS stability preserved
- **Given** the incremental binding logic is active.
- **When** 300+ cards are processed and bound over time.
- **Then** the page SHALL NOT freeze on iOS Safari.
- **And** the main thread SHALL remain responsive (yielding to the browser between chunks via `requestAnimationFrame`).
- **And** the optimization to replace animated loading GIFs with thumbnails before they enter the viewport SHALL remain active to prevent decoding overhead.
