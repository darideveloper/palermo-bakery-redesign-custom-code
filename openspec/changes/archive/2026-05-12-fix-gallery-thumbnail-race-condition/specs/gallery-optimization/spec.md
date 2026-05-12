# Spec: Gallery Optimization

## MODIFIED Requirements

### Image Resolution Management
The gallery SHALL use optimized thumbnails for the grid preview while providing high-resolution images for the lightbox.

#### Requirement: Server-Side Thumbnail Source
When the server renders the gallery page, it SHALL generate `<img src="...">` attributes with `-300x300` thumbnail URLs instead of full-resolution URLs.

#### Scenario: Initial Page Load
- **Given** a gallery page with 300+ product images
- **When** the server renders the page HTML
- **Then** each product image's `<img src="...">` SHALL contain the `-300x300` thumbnail URL, NOT the full-resolution URL

#### Scenario: Lightbox HREF Preservation
- **Given** a gallery product image
- **When** the server renders the anchor tag
- **Then** the anchor's `href` attribute SHALL point to the original full-resolution image for the lightbox

#### Scenario: AJAX-Loaded Content
- **Given** new products loaded via infinite scroll or category filter AJAX
- **When** the server processes the AJAX request
- **Then** the new products' `<img src="...">` SHALL also contain `-300x300` thumbnail URLs

#### Requirement: Common Image Size Registry
WooCommerce SHALL be configured to use 300x300 as the `shop_catalog` image size so that generated thumbnails match the gallery grid.

#### Scenario: Thumbnail Dimensions
- **Given** the WooCommerce catalog image size configuration
- **When** the gallery page loads
- **Then** the `shop_catalog` image size SHALL be set to 300x300 with cropping enabled

### Requirement: Lightbox Resolution Preservation
The lightbox SHALL always display the full-resolution version of the image.

#### Scenario: Lightbox Source
- **Given** a product image in the grid
- **When** the gallery prepares the lightbox link
- **Then** the anchor's `href` attribute SHALL point to the original full-resolution image (e.g., `image-scaled.jpg`), NOT the thumbnail

### Requirement: Client-Side Fallback
The gallery script SHALL serve as a fallback for any images not handled by the server-side filter.

#### Scenario: Dynamic Content Handling
- **Given** gallery images loaded dynamically via AJAX
- **When** the script processes new items
- **Then** the script SHALL sync `data-original`, `data-src`, `data-lazy-src` to the thumbnail URL

## MODIFIED Requirements

### Requirement: Lazy Loader Conflict Prevention
The script SHALL prevent the theme's `jquery.lazyload.js` from interfering with optimized thumbnail URLs.

#### Scenario: Lazy Class Removal
- **Given** a gallery image with `class="lazy"`
- **When** the script applies the optimized thumbnail URL
- **Then** the `lazy` class SHALL be removed to prevent the lazy loader from intercepting the `src` change
