# modal-cake-color-sync Specification

## Purpose
TBD - created by archiving change fix-modal-navigation-cake-color. Update Purpose after archive.
## Requirements
### Requirement: Modal Image Color Synchronization
The modal lightbox image and caption SHALL synchronize their color variant (White or Ivory) with the current label displayed in the modal caption.

#### Scenario: Modal Navigation Sync
- **WHEN** the user navigates to a different cake variant inside the modal using the next or previous arrows
- **AND** the modal caption text is updated to include either "White" or "Ivory"
- **THEN** the modal image source and caption text MUST be updated within 250ms to match the detected color variant
- **AND** the update MUST use the established filename and label replacement rules (e.g., swapping "White" and "Ivory", handling special cases like Pindots).

### Requirement: Modal Opening Sync
The modal lightbox SHALL display the correct color variant of the cake immediately upon opening, matching the state of the "White/Ivory" checkboxes.

#### Scenario: Opening Modal with Ivory Selected
- **WHEN** the user has the "Ivory" checkbox selected
- **AND** the user clicks a gallery image to open the modal
- **THEN** the modal image MUST display the "Ivory" variant of the selected cake.

### Requirement: Modal Loading Feedback
The modal lightbox SHALL provide visual feedback when a new cake image is loading.

#### Scenario: Loader shown on variant switch
- **WHEN** the user navigates to a different cake variant or changes the color while the modal is open
- **THEN** a loading spinner MUST be displayed over the modal image area
- **AND** the spinner MUST be hidden once the new image has fully loaded.

