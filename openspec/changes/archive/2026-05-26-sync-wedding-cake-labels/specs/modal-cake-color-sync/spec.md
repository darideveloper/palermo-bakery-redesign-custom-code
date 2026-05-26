## MODIFIED Requirements

### Requirement: Modal Image Color Synchronization
The modal lightbox image and caption SHALL synchronize their color variant (White or Ivory) with the current label displayed in the modal caption.

#### Scenario: Modal Navigation Sync
- **WHEN** the user navigates to a different cake variant inside the modal using the next or previous arrows
- **AND** the modal caption text is updated to include either "White" or "Ivory"
- **THEN** the modal image source and caption text MUST be updated within 250ms to match the detected color variant
- **AND** the update MUST use the established filename and label replacement rules (e.g., swapping "White" and "Ivory", handling special cases like Pindots).
