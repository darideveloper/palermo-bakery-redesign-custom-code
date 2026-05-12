# Spec: Gallery Optimization

## MODIFIED Requirements

### Image Resolution Management
The gallery SHALL use optimized thumbnails for the grid preview while providing high-resolution images for the lightbox.

#### Requirement: Automatic Thumbnail Switching
When the gallery initializes or refreshes, it SHALL transform the product image sources to use 300x300 thumbnails.

#### Scenario: Grid Preview Resolution
- **Given** a product image in the grid with source `image-scaled.jpg`
- **When** the gallery initializes
- **Then** the image's `src` attribute SHALL be updated to `image-300x300.jpg` (the `-scaled` suffix is stripped before appending `-300x300`)

#### Scenario: Grid Preview Resolution (already optimized)
- **Given** a product image in the grid with source already set to `image-300x300.jpg`
- **When** the gallery initializes
- **Then** the image's `src` attribute SHALL NOT be modified (preventing double-processing)

#### Requirement: Lightbox Resolution Preservation
The lightbox SHALL always display the full-resolution version of the image.

#### Scenario: Lightbox Source
- **Given** a product image in the grid
- **When** the gallery prepares the lightbox link
- **Then** the anchor's `href` attribute SHALL point to the original full-resolution image (e.g., `image-scaled.jpg`), NOT the thumbnail

## ADDED Requirements

### Requirement: Lazy Loader Conflict Prevention
The script SHALL prevent the theme's `jquery.lazyload.js` from reverting optimized thumbnail URLs back to high-resolution URLs.

#### Scenario: Lazy Class Removal
- **Given** a gallery image with `class="lazy"` and high-res `data-original`
- **When** the script applies the optimized thumbnail URL
- **Then** the `lazy` class SHALL be removed first to prevent the lazy loader from intercepting the `src` change

#### Scenario: Attribute Update Order
- **Given** a gallery image being processed
- **When** the script modifies the image attributes
- **Then** the `lazy` class SHALL be removed BEFORE updating `src`/`data-original`/`data-src` to avoid race conditions with MutationObserver-based lazy loaders
