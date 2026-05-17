## Context

The project contains approximately 22 custom CSS, JS, and HTML files located in the root directory. These files are enqueued in WordPress via `functions.php`. Currently, there is no organizational structure, making it difficult to distinguish between core layout overrides and specific feature implementations (like the Favorites system or Wedding Cake color switcher).

## Goals / Non-Goals

**Goals:**
- Move all custom files from root to a structured `src/` directory.
- Delete redundant files (`function.php`, `fix-gallery-thumbnail-src.php`, `woocomerce-disable-proucts-page.php`) and test files (`test-popup-form.html`).
- Relocate `functions.php` logic to `src/core/functions.php` for better curation.
- Categorize files by feature or core function.
- Ensure all WordPress enqueues are updated to point to the new locations.

**Non-Goals:**
- Do not change the internal logic or code content of any file.
- Do not merge files or change their modularity.
- Do not delete any functionality.

## Decisions

### 1. Directory Structure
We will adopt a two-tier organizational strategy under a new `src/` directory:
- **`src/core/`**: For foundational overrides that affect the entire site (Branding, Layout, Base Forms).
- **`src/features/`**: For modular enhancements that implement specific user-facing functionality.
- **`src/assets/`**: For third-party imports and static resources.

### 2. File Mapping Strategy
Files will be moved according to the following mapping:

| Category | Feature/Module | Files |
|---|---|---|
| **Assets** | Icons | `import-font-awesome-icons.html` |
| **Core** | Branding | `logo-styles.css` |
| | Forms | `footer-form-style.css`, `form-style.css` |
| | Layout | `layout-and-hidden-elements.css`, `separator.css` |
| **Features** | Animations | `aos-import.html`, `aos-init.js` |
| | Auth | `custom-auth.css` |
| | Categories | `category-filter-menu.css`, `category-loader.css`, `category-loader.js` |
| | Favorites | `fav-button.js`, `favorite-page.css`, `header-fav.css` |
| | Gallery | `grid-loading-spinner.css`, `product-gallery.css` |
| | Lightbox | `image-lightbox.js`, `modal-custom.css` |
| | Order Cake | `order-wedding-cake-change-cake-color.js` |
| | Popup Form | `custom-popup-form.css`, `custom-popup-form.js` |

### 3. Path Resolution and Curation
The main `functions.php` in the root will now act as a clean loader. Its entire functional logic is moved to `src/core/functions.php`. All asset enqueue paths are updated to point to their new locations under `src/`.

## Risks / Trade-offs

- **[Risk] Path Errors**: Moving over 20 files increases the risk of a typo in `functions.php`, leading to 404s for assets.
  - **Mitigation**: Systematic implementation where each file move is immediately followed by its path update in `functions.php`, followed by a verification step.
- **[Risk] Browser Caching**: Users might still have the old file paths cached or experience issues if the move happens while they are browsing.
  - **Mitigation**: Standard WordPress versioning in `wp_enqueue_style/script` (already handled by theme constants) will help, but a simple cache clear on the server is recommended after the move.
