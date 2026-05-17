## 1. Implementation

- [x] 1.1 Add the global event listener to `image-lightbox.js` to intercept `.pp_close` clicks.
- [x] 1.2 Implement the redirection logic: `e.stopImmediatePropagation()`, `e.preventDefault()`, and a safe `document.querySelector('.pp_overlay')?.click()`.
- [x] 1.3 Ensure the listener is attached with the `capture` phase to intercept before the library's listeners.
- [x] 1.4 Hide `.pp_expand` globally in `product-gallery.css` and remove redundant media query rules.

## 2. Verification

- [x] 2.1 Manually verify on desktop that clicking the "X" button closes the lightbox.
- [x] 2.2 Manually verify on mobile that clicking the "X" button closes the lightbox.
- [x] 2.3 Verify that clicking the overlay still closes the lightbox as expected.
