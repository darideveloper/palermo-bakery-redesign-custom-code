# Proposal: Update Custom Popup Form to use Contact Form 7

## Problem Statement
The current custom popup form injects a hardcoded HTML structure and uses a proprietary API for submissions. The user wants to switch to an existing Contact Form 7 (CF7) form that is already present in the DOM (rendered by a WordPress plugin). The new solution must be scalable, maintaining styling and functionality even if the CF7 form fields change, and it must integrate with CF7's native submission logic while preserving the current "cupcake" trigger and popup animation.

## Proposed Changes

### Logic Update (`custom-popup-form.js`)
- **Remove HTML Injection of Form**: Stop injecting the form's internal HTML. Instead, only inject the popup wrapper (`#custom-popup-wrapper`), the trigger button (`#form-trigger-btn`), and the popup container structure.
- **Dynamic Form Relocation**: On `DOMContentLoaded`, identify the CF7 form in the DOM (prioritizing `#wpcf7-f1874-o1`) and move it into the `#popup-form-container`.
- **Prevent Form Flashing**: Implement CSS to hide the source CF7 container until it is successfully moved to the popup to avoid visual glitches.
- **Native WordPress Submission**: Remove all custom `fetch` or `XMLHttpRequest` logic. The form submission will be handled entirely by the Contact Form 7 plugin and WordPress.
- **Scalable Wrapper Handling**: Apply "reset" styles to CF7's native `<p>` and `<br>` wrappers to ensure consistent spacing regardless of the number of fields added.
- **UI Synchronization**: Use CF7-specific JavaScript events (e.g., `wpcf7mailsent`) for popup-specific UI actions like auto-closing the container and styling native validation tips.
- **Maintain Trigger Logic**: Keep the toggle functionality for showing/hiding the popup.

### Style Update (`custom-popup-form.css`)
- **Generic CF7 Styling**: Update CSS selectors to target CF7's native classes (`.wpcf7-form`, `.wpcf7-form-control`, `.wpcf7-submit`, etc.) to ensure consistent appearance regardless of field changes.
- **Form Layout Refinement**: Ensure the CF7 form fills the popup container correctly, applying appropriate padding and vertical spacing between fields.
- **Responsive Integrity**: Ensure the CF7 form remains usable and visually appealing in the full-screen mobile modal.

## Impact
- **Maintenance**: Easier to manage form fields via the WordPress CF7 plugin without touching JS/CSS code.
- **Functionality**: Uses the established CF7 submission pipeline, including validation and email notifications.
- **User Experience**: Preserves the custom "Palermo Bakery" aesthetic (cupcake button, smooth transitions) while using a standard form engine.

## Verification Plan
- **Manual Test**: Confirm the cupcake button appears and toggles the popup.
- **Manual Test**: Confirm the CF7 form is correctly rendered inside the popup.
- **Manual Test**: Submit the form and verify that the CF7 success/error feedback is visible and the popup auto-closes on success.
- **Responsive Test**: Verify the full-screen modal behavior on screens < 600px.
