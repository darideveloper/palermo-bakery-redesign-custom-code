# Design: Lightbox Fav Spec Alignment

## Specification Promotion
The feature specification is promoted from the temporary archive to the permanent specification directory.

- **Destination**: `/openspec/specs/lightbox-fav-button/spec.md`

## Style Requirements
The `#lightbox-fav-btn` inherits basic properties from `.my-custom-fav-btn` but defines a specific shadow override in `@product-gallery.css`.

### Shadow Specification
- **CSS Rule**: `#lightbox-fav-btn`
- **Shadow Value**: `rgba(0, 0, 0, 0.4) 0px 7px 20px 1px`
- **Rationale**: The lightbox button requires a more prominent shadow than the gallery card version to remain visible across a wide variety of full-resolution image backgrounds (bright highlights, textures, etc.).
