# Spec: Form Frontend

## MODIFIED Requirements

### Requirement: The application MUST render a custom popup contact form triggered by a cupcake floating button
The application MUST render a custom popup contact form triggered by a floating button styled as a cupcake with a toothpick flag that says "Ask Me", using HTML, CSS, and JS. The popup container's visibility MUST be toggled using a specific `.popup-hidden` class to avoid styling collisions with WordPress global utility classes. The trigger button MUST be positioned at bottom `24px` and right `65px` for better visual alignment.

#### Scenario: User clicks the floating button
- Given the user is on the site
- When the user clicks the `#form-trigger-btn` (cupcake icon)
- Then the `#popup-form-container` becomes visible with a smooth transition (by removing the `.popup-hidden` class)
- And the form displays the text: "Do you want to order a cake or have any questions for one of our cake consultants? Fill out this form and they will contact within 24-48 hours."
- And the form displays inputs for Name, Email, Phone, Message, and a submit button.

### Requirement: The contact form MUST contain hidden fields for the proprietary API
The contact form MUST contain specific hidden fields required to submit data directly to the proprietary backend API, ensuring the credentials reflect the correct production values.

#### Scenario: Submitting the form to the proprietary API
- Given the form is rendered
- Then the form includes hidden inputs for `api_key`, `user`, `subject`, and `redirect` with the final production values
- And the submit action handles the data via JavaScript using `fetch()`.