# form-frontend Specification

## Purpose
TBD - created by archiving change add-custom-popup-form. Update Purpose after archive.
## Requirements
### Requirement: The application MUST render a custom popup contact form triggered by a cupcake floating button
The application MUST render a custom popup contact form triggered by a floating button styled as a cupcake with a toothpick flag that says "Ask Me", using HTML, CSS, and JS.

#### Scenario: User clicks the floating button
- Given the user is on the site
- When the user clicks the `#form-trigger-btn` (cupcake icon)
- Then the `#popup-form-container` becomes visible with a smooth transition
- And the form displays the text: "Do you want to order a cake or have any questions for one of our cake consultants? Fill out this form and they will contact within 24-48 hours."
- And the form displays inputs for Name, Email, Phone, Message, and a submit button.

### Requirement: The contact form MUST contain hidden fields for the proprietary API
The contact form MUST contain specific hidden fields required to submit data directly to the proprietary backend API.

#### Scenario: Submitting the form to the proprietary API
- Given the form is rendered
- Then the form includes hidden inputs: `api_key`, `user`, `subject`, and `redirect`
- And the submit action handles the data via JavaScript using `fetch()`.

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

