# form-frontend Specification Update

## MODIFIED Requirements

### Requirement: The application MUST render a custom popup contact form triggered by a cupcake floating button
The application MUST render a custom popup contact form triggered by a floating button styled as a cupcake. Instead of injecting its own form, the application MUST identify an existing Contact Form 7 form in the DOM and relocate it into the popup container.

#### Scenario: User clicks the floating button
- Given the user is on the site and a Contact Form 7 form (`.wpcf7-form`) is present in the DOM
- When the application loads
- Then the CF7 form is moved into the `#popup-form-container`
- And when the user clicks the `#form-trigger-btn`
- Then the `#popup-form-container` becomes visible, displaying the relocated CF7 form.

### Requirement: The application MUST allow WordPress/Contact Form 7 to manage form submission
The application MUST NOT intercept or handle form submission logic. It MUST rely entirely on the native Contact Form 7 plugin and WordPress backend for processing data, validation, and email delivery.

#### Scenario: Submitting the CF7 form
- Given the popup form is open
- When the user submits the form
- Then the WordPress Contact Form 7 plugin handles the request natively via its own AJAX mechanism
- And the application only listens for the `wpcf7mailsent` event to trigger container-level actions (like closing the popup).

## REMOVED Requirements

### Requirement: The contact form MUST contain hidden fields for the proprietary API
*Reason: This requirement is obsolete as the form is now managed and submitted via Contact Form 7's native mechanism.*
