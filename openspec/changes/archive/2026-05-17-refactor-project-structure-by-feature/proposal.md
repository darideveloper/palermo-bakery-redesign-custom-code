## Why

The project root directory is currently cluttered with over 20 standalone CSS, JS, and HTML files. While these files implement distinct and valuable features (Favorites system, Gallery optimizations, Popup forms, etc.), their lack of organization makes it difficult to navigate the codebase, understand feature dependencies, and maintain the project long-term. Organizing these files into a feature-based directory structure will improve developer productivity and codebase clarity.

## What Changes

- Create a structured `src/` directory to house all custom assets.
- Move existing CSS, JS, and HTML files from the root directory into feature-specific subfolders within `src/features/` or `src/core/`.
- Organize shared or generic assets (like Font Awesome imports) into a `src/assets/` directory.
- **NOTE**: As per requirements, the internal logic and content of these files will remain unchanged, focusing strictly on structural reorganization. (Path references in `functions.php` or other loader files will be updated to reflect the new locations).

## Capabilities

### New Capabilities
- `project-organization`: Defines the standard for feature-based file organization and provides a clean, scalable directory structure for current and future enhancements.

### Modified Capabilities
- None: This change is structural and does not alter the requirements or behavior of existing features.

## Impact

- **File Structure**: Significant movement of files from root to `src/`, and deletion of redundant files (`function.php`, `fix-gallery-thumbnail-src.php`, `woocomerce-disable-proucts-page.php`, `test-popup-form.html`).
- **Curated Logic**: The primary logic of `functions.php` is moved to `src/core/functions.php`, leaving a minimal loader in the root.
- **Development Workflow**: Developers will navigate a clean, feature-based directory structure under `src/`.
