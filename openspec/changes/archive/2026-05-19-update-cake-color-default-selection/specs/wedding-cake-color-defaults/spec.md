## ADDED Requirements

### Requirement: Default Color Selection
The wedding cake order form SHALL automatically select the "White" color option upon page load.

#### Scenario: Page Load Default Selection
- **WHEN** the user navigates to a wedding cake order page
- **THEN** the "White" color checkbox MUST be checked 100ms after the DOM is ready
- **AND** all associated image updates for the "White" color variant MUST be triggered

### Requirement: Color Selection Exclusivity
The "White" and "Ivory" color options SHALL be mutually exclusive.

#### Scenario: Switching Colors
- **WHEN** the user clicks the "Ivory" checkbox while "White" is selected
- **THEN** the "White" checkbox MUST be unchecked
- **AND** the "Ivory" checkbox MUST be checked
- **AND** the cake images MUST update to their "Ivory" variants
