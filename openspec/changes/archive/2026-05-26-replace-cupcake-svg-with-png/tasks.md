## 1. Update DOM Structure

- [x] 1.1 In `src/features/popup-form/custom-popup-form.js`, locate the `popupShellHTML` variable.
- [x] 1.2 Replace the `<svg class="cupcake-svg">` block with an `<img class="cupcake-img" src="https://ccdev2026.wpenginepowered.com/wp-content/uploads/2026/05/cupcake-help-icon-120.png" alt="Ask Me">` element.

## 2. Update CSS Styling

- [x] 2.1 In `src/features/popup-form/custom-popup-form.css`, find the `.cupcake-svg` selector.
- [x] 2.2 Rename the selector to `.cupcake-img`.
- [x] 2.3 Update styling to ensure the image scales correctly (`width: 100%; height: 100%; object-fit: contain;`) while retaining the existing `filter: drop-shadow(...)`.
- [x] 2.4 Remove any SVG-specific CSS properties (like `fill` or `stroke`) if they were present inside `.cupcake-svg`.
