# Fix Popup Animation Conflict

## Summary
Rename the generic `.hidden` CSS class used for toggling the custom popup form's visibility to a more specific `.popup-hidden` class. Additionally, the form's HTML structure is now injected dynamically via JavaScript to bypass WordPress admin editor bugs (such as the nested textarea issue) and ensure production-ready API credentials are used securely.

## Motivation
When the custom popup form scripts were uploaded to WordPress, two issues were identified:
1. **Animation Conflict:** WordPress core or page builders defining `.hidden` as a global utility class (often applying `display: none !important;`) broke smooth transitions.
2. **Editor Bug:** Including a `<textarea>` tag inside the "Add HTML" section of the custom code plugin caused the WordPress editor to truncate the code prematurely, breaking the admin interface.

By renaming the class to `.popup-hidden` and moving the HTML structure into `custom-popup-form.js` for dynamic injection, we resolve both the animation collision and the WordPress editor limitation while maintaining a clean implementation.