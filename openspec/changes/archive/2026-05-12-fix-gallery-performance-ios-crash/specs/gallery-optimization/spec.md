## MODIFIED Requirements

### Requirement: Lazy Loader Conflict Prevention
The script SHALL preserve the theme's `jquery.lazyload.js` functionality by keeping the `lazy` CSS class on gallery images and NOT setting the `src` attribute directly. The script SHALL only update `data-original` (and related data attributes) to point to the 300x300 thumbnail URL, allowing lazyload to copy the thumbnail URL to `src` when the image scrolls into view.

#### Scenario: Image processing preserves lazyload
- **Given** a gallery image with `class="lazy"`, `src="...loading.gif"`, and `data-original="...full-image.jpg"`
- **When** the gallery script processes the image
- **Then** the `lazy` class SHALL remain on the image
- **And** the `src` attribute SHALL NOT be changed by the script
- **And** `data-original` SHALL be updated to point to the `-300x300` thumbnail URL

#### Scenario: Lazyload loads thumbnail on scroll
- **Given** an unprocessed gallery image with `class="lazy"` and `data-original="...-300x300.jpg"`
- **When** the user scrolls the image into the viewport
- **Then** the theme's lazyload SHALL copy `data-original` to `src`
- **And** the browser SHALL load the 300x300 thumbnail

## ADDED Requirements

### Requirement: Lightbox Uses Full-Resolution Image
The gallery script SHALL open the prettyPhoto lightbox with the full-resolution version of the image, not the 300x300 thumbnail.

#### Scenario: Lightbox displays full-res image
- **Given** a gallery image link with `data-lightbox-src="...full-image.jpg"` (or a resolvable full URL)
- **When** the user clicks the gallery image
- **Then** the lightbox SHALL display the full-resolution version
- **And** the image SHALL NOT appear pixelated or blurry when expanded

#### Scenario: PHP filter preserves full URL for lightbox
- **Given** the PHP output buffer filter rewrites `data-original` to the `-300x300` URL
- **When** the filter processes an `<img>` tag with `data-original`
- **Then** the filter SHALL add a `data-lightbox-src` attribute containing the original full-resolution URL
- **And** `data-original` SHALL still point to the `-300x300` thumbnail

### Requirement: Gallery Page Does Not Crash on iOS Safari
The gallery page SHALL load images lazily so that iOS Safari's memory limit is not exceeded.

#### Scenario: Mobile Safari loads images on scroll
- **Given** the gallery page with 297 products on a mobile device
- **When** the page loads
- **Then** only the initially visible images SHALL begin downloading
- **And** off-screen images SHALL NOT start downloading
- **And** the page SHALL NOT crash due to memory pressure
