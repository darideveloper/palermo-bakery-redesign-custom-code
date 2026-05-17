# Proposal: Align Lightbox Fav Shadow Specification

## Problem
A design refinement was manually applied to the lightbox favorite button's shadow in `@product-gallery.css` to improve visibility against high-resolution images. The current project specifications (archived) do not reflect this change, and the feature itself has not yet been promoted to a permanent project specification.

## Solution
This proposal formalizes the promotion of the `lightbox-fav-button` feature to a root specification and updates the styling requirements to match the current implementation.

### Key Actions
1. **Promote Specification**: Move the `lightbox-fav-button` spec from the archive to `/openspec/specs/`.
2. **Update Style Requirements**: Explicitly document the deeper shadow (`rgba(0, 0, 0, 0.4) 0px 7px 20px 1px`) used for the lightbox version of the button.

## Benefits
- Ensures documentation accurately reflects the live codebase.
- Protects the design intent (high-contrast shadow for lightboxes) during future refactors.
