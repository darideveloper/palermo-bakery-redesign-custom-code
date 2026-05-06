# Design: Contact Form 7 Integration for Custom Popup

## Architecture Overview
The solution moves from a static, JS-injected form to a dynamic, DOM-based integration. We decouple the popup "shell" (trigger and container) from the "content" (the form itself).

### Component: Popup Shell Injection
The script will still inject the following structure at the end of `<body>`:
```html
<div id="custom-popup-wrapper">
    <button id="form-trigger-btn">...</button>
    <div id="popup-form-container" class="popup-hidden">
        <div class="popup-header">
            <button id="close-popup-btn">&times;</button>
        </div>
        <div class="popup-content">
            <!-- Form will be moved here -->
            <div id="popup-form-response" class="popup-hidden"></div>
        </div>
    </div>
</div>
```

### Component: Form Relocation
Since CF7 forms are typically rendered by WordPress into specific sections, the script will:
1. Locate the form using `document.querySelector('#wpcf7-f1874-o1 .wpcf7-form')` or a generic `.wpcf7-form` as fallback.
2. Append it to `.popup-content`.
3. **Flashing Prevention**: Use a CSS selector to set `display: none` on the source container (e.g., `div[data-name="contact-popup"]`) until the form has been successfully moved into the fixed popup wrapper.

### Component: Event Handling
The submission lifecycle is managed entirely by the WordPress Contact Form 7 plugin. The custom script will only observe native CF7 DOM events:
- `wpcf7mailsent`: To trigger the auto-closing of the popup container.
- `wpcf7submit`: To ensure the popup stays open during processing.
- `wpcf7invalid`: To ensure any validation errors are visible within the container.

### Component: CSS Mapping
We will map our existing "Palermo" form styles to CF7 elements, with a focus on scalable layout:
- `label` -> `label`
- `input[type="text"]`, etc. -> `.wpcf7-form-control`
- `button[type="submit"]` -> `.wpcf7-submit`
- **Wrapper Reset**: Set `.wpcf7-form p { margin: 0 0 15px 0; }` and `.wpcf7-form br { display: none; }` to override CF7's default legacy formatting.
- **Feedback Styling**: Style `.wpcf7-response-output`, `.wpcf7-not-valid-tip`, and `.wpcf7-spinner` to match the site's dark/gold color palette.

## Trade-offs and Considerations
- **Multiple Forms**: If the page has other CF7 forms (e.g., in the footer), we must be careful not to "steal" the wrong one. We should prioritize a form with a specific ID if provided, or the first one found that isn't already inside a footer/sidebar. *Decision: Target `.wpcf7-form` and move it. If multiple exist, the user should ensure the one intended for the popup is unique or we use a more specific selector like `div[data-name="popup-form"] .wpcf7-form`.*
- **DOM Loading**: CF7 initializes its own JS. Moving the form element in the DOM might disrupt CF7's internal event listeners if not done carefully. *Decision: Move the element early but ensure we don't break CF7's registration.*
