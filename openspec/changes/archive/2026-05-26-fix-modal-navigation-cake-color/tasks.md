## 1. Code Preparation

- [x] 1.1 Update CSS selector for `cakeImages` in `src/features/order-cake/order-wedding-cake-change-cake-color.js` to include `.product-image-lightbox-image`.

## 2. Modal Synchronization

- [x] 2.1 Implement `syncModalImageColor` function that reads the modal caption and updates the modal image source.
- [x] 2.2 Attach event listeners to `.product-image-lightbox-next` and `.product-image-lightbox-previous` using event delegation.
- [x] 2.3 Add a 250ms delay in the navigation listeners to wait for caption updates.
- [x] 2.4 Add initialization logic to sync the modal image color when the modal is first opened.

## 3. Debugging and Verification

- [x] 3.1 Add logging to track modal navigation events and caption detection.
- [x] 3.2 Add logging to track image source transformations in the modal.
- [x] 3.3 Verify synchronization when switching between White and Ivory via checkboxes while the modal is open.
- [x] 3.4 Verify synchronization when navigating between different cake variants using modal arrows.

## 4. Modal Loader and Default Selection Fix

- [x] 4.1 Implement robust default selection of "White" on page load (avoiding double clicks).
- [x] 4.2 Define CSS for a loading spinner suitable for the modal.
- [x] 4.3 Implement logic to inject and toggle the loading spinner in the modal.
- [x] 4.4 Attach 'load' event listener to the modal image to hide the spinner.
- [x] 4.5 Verify the loader shows on image change and hides on load.
- [x] 4.6 Verify "White" is selected by default without redundant clicks.
