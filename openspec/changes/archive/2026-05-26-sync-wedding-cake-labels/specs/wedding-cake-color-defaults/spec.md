## MODIFIED Requirements

### Requirement: Default Color Selection
The wedding cake order form SHALL automatically select the "White" color option upon page load if no color is already selected.

#### Scenario: Page Load Robust Default Selection
- **WHEN** the user navigates to a wedding cake order page
- **AND** the "White" checkbox is NOT already checked
- **THEN** the "White" color checkbox MUST be clicked 100ms after the DOM is ready
- **AND** all associated image, label, and form value updates for the "White" color variant MUST be triggered.

### Requirement: Color Selection Exclusivity
The "White" and "Ivory" color options SHALL be mutually exclusive.

#### Scenario: Switching Colors
- **WHEN** the user clicks the "Ivory" checkbox while "White" is selected
- **THEN** the "White" checkbox MUST be unchecked
- **AND** the "Ivory" checkbox MUST be checked
- **AND** the cake images, labels, and form values MUST update to their "Ivory" variants
- **AND** if a modal lightbox is currently open, the modal image and caption MUST also update to their "Ivory" variants.
