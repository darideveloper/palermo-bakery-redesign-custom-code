# Tasks

- [x] Create `custom-popup-form.html` scaffolding the form container, the introductory text ("Do you want to order a cake or have any questions for one of our cake consultants? Fill out this form and they will contact within 24-48 hours."), and the form inputs (Name, Email, Phone, Message).
- [x] Add the hidden fields (`api_key`, `user`, `subject`, `redirect`) required by the proprietary API to `custom-popup-form.html`.
- [x] Create the HTML structure for the floating trigger button, styling it as a cupcake with a toothpick flag reading "Ask Me".
- [x] Create `custom-popup-form.css` and implement the positioning (fixed bottom right), z-index (`9999`), and smooth visibility toggling for the form container.
- [x] Add CSS in `custom-popup-form.css` to visually style the cupcake button and the "Ask Me" toothpick flag (using pure CSS or inline SVG).
- [x] Update `form-style.css` to append `#my-proprietary-form` to existing input and textarea selectors to inherit the grid layout styling.
- [x] Update `footer-form-style.css` to append `#my-proprietary-form` to existing submit button selectors to inherit the button styling.
- [x] Create `custom-popup-form.js` to toggle the form container's visibility when the cupcake button is clicked.
- [x] Implement the form submission logic in `custom-popup-form.js` using `fetch()` to post the data to the API and prevent the default form submission.
- [x] Create a `test-popup-form.html` file that links the existing form CSS (`form-style.css`, `footer-form-style.css`), the newly created CSS and JS files, and includes the popup HTML structure to allow for local visual and API testing outside of WordPress.
- [x] Add vertical spacing (`margin-bottom: 15px`) between form fields and labels in `custom-popup-form.css` to improve readability.
- [x] Implement a mobile-first responsive design in `custom-popup-form.css` that converts the floating popup into a full-screen modal on screens narrower than 600px.