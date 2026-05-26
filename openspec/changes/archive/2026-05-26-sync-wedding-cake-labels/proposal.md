## Why

Currently, when switching between "White" and "Ivory" variants on the wedding cake order page, the images update correctly but the textual labels (cake names) and form submission values remain static. This creates a confusing user experience where the visual representation does not match the text, and potentially results in incorrect color data being submitted.

## What Changes

- **Label and Image Synchronization via Variant Selection**: Instead of manually renaming DOM elements, the system will now automatically select the matching hidden radio button for each cake variant. This ensures that both the image and the label update natively through the existing gallery plugin.
- **Form Data Integrity**: By clicking the actual variant radio buttons, the correct product data is automatically associated with the form submission.
- **Modal Caption Sync**: The caption displayed in the modal lightbox will be updated when the color variant is toggled or when navigating between cakes.
- **Loop Prevention**: Implemented an asynchronous synchronization lock to prevent infinite recursion between DOM observers and state updates.

## Capabilities

### New Capabilities
- `wedding-cake-label-sync`: Automatically synchronizes all textual representations of the cake (labels, alt text, form values, and captions) with the globally selected color variant (White/Ivory) using a native-first variant selection strategy.

### Modified Capabilities
- `wedding-cake-color-defaults`: Requirement expanded to include textual label and form value synchronization during the initial "White" selection on page load.
- `modal-cake-color-sync`: Requirement expanded to include modal caption text synchronization and robust navigation handling.

## Impact

- **Codebase**: `src/features/order-cake/order-wedding-cake-change-cake-color.js` was completely refactored to prioritize variant-radio interaction over manual DOM manipulation.
- **Stability**: Resolved critical issues related to element duplication and browser thread freezing.
- **Form Submission**: Correct color variant names are now natively passed in the "cake" field of the order form.
