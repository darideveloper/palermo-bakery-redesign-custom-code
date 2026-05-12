# gallery-optimization Specification

## Purpose
TBD - created by archiving change optimize-gallery-images-resolution. Update Purpose after archive.
## Requirements
### Requirement: Lazy Loader Conflict Prevention
The script SHALL prevent the theme's `jquery.lazyload.js` from interfering with optimized thumbnail URLs.

#### Scenario: Lazy Class Removal
- **Given** a gallery image with `class="lazy"`
- **When** the script applies the optimized thumbnail URL
- **Then** the `lazy` class SHALL be removed to prevent the lazy loader from intercepting the `src` change

