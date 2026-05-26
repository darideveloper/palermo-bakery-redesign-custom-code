## 1. Research and Scaffolding

- [x] 1.1 Rename `getCakeImages` to `getCakeElements` in `src/features/order-cake/order-wedding-cake-change-cake-color.js` and expand the selector to include `input[name="cake"]`, `label span`, and `.product-image-lightbox-caption`.

## 2. Core Implementation (Refactored)

- [x] 2.1 Update `replaceColor` helper to explicitly guard "Exquisite White Wedding Cake" to prevent it from being swapped to "Ivory".
- [x] 2.2 Implement `syncGlobalColor` to prioritize clicking variant radio buttons over manual DOM modification.
- [x] 2.3 Implement legacy fallback logic for single-variant products.
- [x] 2.4 Implement `isSyncing` lock with 200ms `setTimeout` to prevent recursive loops and page freezes.

## 3. Modal Synchronization

- [x] 3.1 Update `handleModalImageSrcChange` to include synchronization logic for the `.product-image-lightbox-caption` text content.
- [x] 3.2 Update `MutationObserver` logic to ensure caption synchronization is triggered on both attribute changes and child list modifications.

## 4. Verification

- [x] 4.1 Verify that toggling "Ivory" updates all visible cake labels on the main grid without duplication.
- [x] 4.2 Verify that the radio button selection in the hidden form is correctly updated.
- [x] 4.3 Verify that opening the modal and toggling color updates both the image and the caption.
- [x] 4.4 Verify that the page remains stable and responsive (no freezes) during rapid toggling and modal navigation.
