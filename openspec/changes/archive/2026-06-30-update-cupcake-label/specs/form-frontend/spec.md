## MODIFIED Requirements

### Requirement: The application MUST render a custom popup contact form triggered by a cupcake floating button
The application MUST render a custom popup contact form triggered by a floating button styled as a cupcake with a toothpick flag that says "Contact Us", using HTML, CSS, and JS. Instead of injecting its own form, the application MUST identify an existing Contact Form 7 form in the DOM and relocate it into the popup container. The application MUST use an attribute-starts-with selector (`[id^="wpcf7-f1874"]`) to locate the CF7 form, matching any occurrence suffix (`-o1`, `-o2`, etc.) rather than hardcoding a specific suffix. The popup container's visibility MUST be toggled using a specific `.popup-hidden` class to avoid styling collisions with WordPress global utility classes. The trigger button MUST be positioned at bottom `24px` and right `65px` for better visual alignment.

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

#### Scenario: Label reads "Contact Us"
- **WHEN** the floating trigger button is rendered
- **THEN** the toothpick flag displays "Contact Us" and the `aria-label` on the button is "Contact Us"
