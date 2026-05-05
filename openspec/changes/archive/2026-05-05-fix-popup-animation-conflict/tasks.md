# Tasks

- [x] Refactor `custom-popup-form.js` to include the form's HTML structure as a template literal and inject it into the `document.body` on `DOMContentLoaded`.
- [x] Update `custom-popup-form.js` to use the more specific `.popup-hidden` class for all visibility toggling and initial states.
- [x] Update `custom-popup-form.css` to rename the `#popup-form-container.hidden` selector to `#popup-form-container.popup-hidden`.
- [x] Update `#custom-popup-wrapper` in `custom-popup-form.css` to use the finalized coordinates (`bottom: 24px; right: 65px`).
- [x] Update `test-popup-form.html` to remove hardcoded HTML and rely on the dynamic injection from the updated JS file.
- [x] Ensure the injected HTML in `custom-popup-form.js` uses the final production `user` and `api_key` values.