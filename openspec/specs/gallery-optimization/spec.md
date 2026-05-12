# gallery-optimization Specification

## Purpose
TBD - created by archiving change optimize-gallery-images-resolution. Update Purpose after archive.
## Requirements
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

