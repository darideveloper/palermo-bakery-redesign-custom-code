## 1. Directory Structure Setup

- [x] 1.1 Create the root `src/` directory.
- [x] 1.2 Create core subdirectories: `src/core/branding/`, `src/core/forms/`, `src/core/layout/`.
- [x] 1.3 Create feature subdirectories: `src/features/animations/`, `src/features/auth/`, `src/features/categories/`, `src/features/favorites/`, `src/features/gallery/`, `src/features/lightbox/`, `src/features/order-cake/`, `src/features/popup-form/`.
- [x] 1.4 Create asset subdirectories: `src/assets/icons/`.

## 2. Migrating Core and Asset Files

- [x] 2.1 Move `logo-styles.css` to `src/core/branding/`.
- [x] 2.2 Move `footer-form-style.css` and `form-style.css` to `src/core/forms/`.
- [x] 2.3 Move `layout-and-hidden-elements.css` and `separator.css` to `src/core/layout/`.
- [x] 2.4 Move `import-font-awesome-icons.html` to `src/assets/icons/`.

## 3. Migrating Feature Files

- [x] 3.1 Move `aos-import.html` and `aos-init.js` to `src/features/animations/`.
- [x] 3.2 Move `custom-auth.css` to `src/features/auth/`.
- [x] 3.3 Move `category-filter-menu.css`, `category-loader.css`, and `category-loader.js` to `src/features/categories/`.
- [x] 3.4 Move `fav-button.js`, `favorite-page.css`, and `header-fav.css` to `src/features/favorites/`.
- [x] 3.5 Move `grid-loading-spinner.css` and `product-gallery.css` to `src/features/gallery/`.
- [x] 3.6 Move `image-lightbox.js` and `modal-custom.css` to `src/features/lightbox/`.
- [x] 3.7 Move `order-wedding-cake-change-cake-color.js` to `src/features/order-cake/`.
- [x] 3.8 Move `custom-popup-form.css` and `custom-popup-form.js` to `src/features/popup-form/`.

## 4. Updating Loaders and Curation

- [x] 4.1 Update all asset enqueue paths in `src/core/functions.php` to point to the new `src/` subfolders.
- [x] 4.2 Relocate the primary logic of `functions.php` to `src/core/functions.php` and create a root loader.
- [x] 4.3 Audit and update any relative file references within the moved HTML/JS files (e.g., `test-popup-form.html` was updated before deletion).

## 5. Verification and Cleanup

- [x] 5.1 Verify the website front-end for 404 errors using browser developer tools.
- [x] 5.2 Delete redundant PHP files from root: `function.php`, `fix-gallery-thumbnail-src.php`, `woocomerce-disable-proucts-page.php`.
- [x] 5.3 Delete temporary test files: `test-popup-form.html`.
- [x] 5.4 Confirm interactive features (Favorites toggle, Lightbox buttons, Popup form trigger) are fully operational.
- [x] 5.5 Remove any empty directories or residual files from the root if necessary.
