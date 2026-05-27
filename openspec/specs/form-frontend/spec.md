# form-frontend Specification

## Purpose
TBD - created by archiving change add-custom-popup-form. Update Purpose after archive.
## Requirements
### Requirement: The application MUST render a custom popup contact form triggered by a cupcake floating button
The application MUST render a custom popup contact form triggered by a floating button styled as a cupcake with a toothpick flag that says "Ask Me", using HTML, CSS, and JS. Instead of injecting its own form, the application MUST identify an existing Contact Form 7 form in the DOM and relocate it into the popup container. The application MUST use an attribute-starts-with selector (`[id^="wpcf7-f1874"]`) to locate the CF7 form, matching any occurrence suffix (`-o1`, `-o2`, etc.) rather than hardcoding a specific suffix. The popup container's visibility MUST be toggled using a specific `.popup-hidden` class to avoid styling collisions with WordPress global utility classes. The trigger button MUST be positioned at bottom `24px` and right `65px` for better visual alignment.

#### Scenario: User clicks the floating button
- Given the user is on the site and a Contact Form 7 form (`.wpcf7-form`) is present in the DOM
- When the application loads
- Then the CF7 form is moved into the `#popup-form-container`
- And when the user clicks the `#form-trigger-btn`
- Then the `#popup-form-container` becomes visible, displaying the relocated CF7 form.

#### Scenario: Popup form works on pages with multiple CF7 forms
- **WHEN** the page contains multiple Contact Form 7 forms and the target form receives an occurrence suffix other than `-o1` (e.g., `#wpcf7-f1874-o2`)
- **THEN** the application still locates and relocates the form using the `[id^="wpcf7-f1874"]` selector
- **AND** the cupcake button is visible and functional
- **AND** the target form is not left visible outside the popup

### Requirement: The application MUST allow WordPress/Contact Form 7 to manage form submission
The application MUST NOT intercept or handle form submission logic. It MUST rely entirely on the native Contact Form 7 plugin and WordPress backend for processing data, validation, and email delivery.

#### Scenario: Submitting the CF7 form
- Given the popup form is open
- When the user submits the form
- Then the WordPress Contact Form 7 plugin handles the request natively via its own AJAX mechanism
- And the application only listens for the `wpcf7mailsent` event to trigger container-level actions (like closing the popup).

### Requirement: The application MUST render the popup as a full-screen modal on mobile devices
The application MUST transform the floating popup into a full-screen modal on screens narrower than 600px to ensure usability.

#### Scenario: User views the form on a mobile device
- Given the user is on a screen narrower than 600px
- When the popup form is open
- Then the `#popup-form-container` becomes a full-screen fixed overlay covering the entire viewport.

### Requirement: The application MUST reuse existing form styles for the new popup form
The application MUST adapt existing CSS selectors from `form-style.css` and `footer-form-style.css` to match the current visual identity without needing duplicate properties, and add proper vertical spacing between fields.

#### Scenario: Form elements inherit global styles
- Given the custom popup form is loaded
- When the browser applies styles
- Then selectors in `form-style.css` and `footer-form-style.css` apply to the new form's inputs and buttons, maintaining visual consistency without duplicating heavy CSS rules.
- And form fields have vertical spacing for improved readability.

### Requirement: The application MUST insulate popup form styling from page-specific theme CSS
The application MUST apply a reset rule to `#popup-form-container .wpcf7-form` that neutralizes page-specific theme overrides, ensuring consistent popup styling across all pages. The reset MUST zero out `padding`, `margin`, `max-width`, `background`, and `border-radius` on the `.wpcf7-form` element inside the popup container.

#### Scenario: Popup form renders consistently on themed pages
- **WHEN** a page has theme-specific CSS targeting `.wpcf7-form` (e.g., `.page-id-1122 .wpcf7-form` with `padding`, `margin`, `max-width`, `background`, `border-radius`)
- **AND** the popup form is open
- **THEN** the `.wpcf7-form` inside `#popup-form-container` is unaffected by those page-specific rules
- **AND** the popup renders with its own consistent padding (25px on the container), background, and border-radius

### Requirement: Trigger Visual Presentation
The popup form trigger MUST use the provided remote PNG image (`https://ccdev2026.wpenginepowered.com/wp-content/uploads/2026/05/cupcake-help-icon-120.png`) instead of an inline SVG, while maintaining its function as a floating toggle button.

#### Scenario: Visual Display
- **WHEN** the floating trigger button is rendered on the page
- **THEN** it displays the specific remote PNG image properly sized and contained within the circular button boundary, rather than an inline SVG.

