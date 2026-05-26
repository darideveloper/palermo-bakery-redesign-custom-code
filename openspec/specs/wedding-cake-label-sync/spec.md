# Capability: wedding-cake-label-sync

## Purpose
Ensure that when switching between "White" and "Ivory" color variants on the wedding cake order page, all textual labels, alt attributes, modal captions, and form submission values are automatically and correctly synchronized to maintain visual and data integrity.

## Requirements

### Requirement: Global Variant Synchronization
The system SHALL synchronize all textual labels and images with the currently selected color variant (White or Ivory) by programmatically selecting the corresponding product variant radio button.

#### Scenario: Sync via Variant Selection
- **WHEN** the user selects the "Ivory" color variant
- **THEN** the system MUST find the radio button containing "Ivory" for every cake in the grid and trigger a `click()` event on it.
- **AND** the gallery plugin SHALL handle the visual and textual update of the cake card.

### Requirement: Legacy Fallback Synchronization
The system SHALL maintain a manual synchronization fallback for cakes that do not have multiple selectable variants.

#### Scenario: Fallback for Single Variant
- **WHEN** no matching radio button is found for a specific cake
- **THEN** the system MUST manually swap "White" and "Ivory" in the `src`, `srcset`, `alt`, and `textContent` of the cake's elements.

### Requirement: Loop Prevention
The system SHALL prevent recursive synchronization loops that could cause the browser to freeze.

#### Scenario: Asynchronous Execution Lock
- **WHEN** a synchronization is triggered
- **THEN** the system MUST set an execution lock (`isSyncing = true`)
- **AND** it MUST ignore all further sync triggers until the lock is released after a minimum delay of 200ms.
