## ADDED Requirements

### Requirement: Organized Directory Structure
The project SHALL maintain an organized directory structure for all custom assets, moving them from the root into a dedicated `src/` directory with subfolders based on core functionality and features.

#### Scenario: Verify src Directory Structure
- **WHEN** the `src/` directory is inspected
- **THEN** it SHALL contain subfolders for `assets`, `core`, and `features`.

### Requirement: Root Directory Curation
The project root SHALL be kept clean by removing redundant or temporary files (`function.php`, `fix-gallery-thumbnail-src.php`, `woocomerce-disable-proucts-page.php`, `test-popup-form.html`).

#### Scenario: Verify Clean Root
- **WHEN** the project root directory is listed
- **THEN** the files `function.php`, `fix-gallery-thumbnail-src.php`, `woocomerce-disable-proucts-page.php`, and `test-popup-form.html` SHALL NOT be present.

### Requirement: Curated Logic Entry Point
The primary theme logic SHALL be relocated from the root `functions.php` to `src/core/functions.php`. The root `functions.php` SHALL serve as a minimal loader that requires the curated core file.

#### Scenario: Theme Logic Loading
- **WHEN** WordPress loads the theme
- **THEN** it SHALL successfully include `src/core/functions.php` via the root `functions.php` loader.

#### Scenario: Discoverability of Feature Files
- **WHEN** a developer navigates to the `src/features/` directory
- **THEN** they SHALL find subdirectories for each major project feature (e.g., `favorites`, `gallery`, `popup-form`) containing the associated CSS and JS files.

### Requirement: Categorization of Core Assets
Foundational site-wide styles and overrides SHALL be categorized within a `src/core/` directory to distinguish them from specific feature-based enhancements.

#### Scenario: Location of Site Overrides
- **WHEN** a developer needs to modify the site logo styles or global layout rules
- **THEN** they SHALL find the relevant files in `src/core/branding/` or `src/core/layout/` instead of the project root.

### Requirement: Persistent Resource Availability
The reorganization SHALL NOT break the loading of assets on the front-end of the website. All internal paths and WordPress enqueues SHALL be updated to reflect the new structure.

#### Scenario: Successful Loading after Refactor
- **WHEN** the website front-end is accessed after the structural move
- **THEN** all custom CSS, JS, and HTML fragments SHALL load from their new paths in the `src/` directory with a 200 OK status.
