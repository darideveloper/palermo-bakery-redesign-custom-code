## Context

The wedding cake order page uses `src/features/order-cake/order-wedding-cake-change-cake-color.js` to handle color variant switching. Initial implementation attempts involved manually renaming text labels and image sources, but this conflicted with the gallery plugin's own DOM management, leading to item duplication and infinite execution loops.

## Goals / Non-Goals

**Goals:**
- Synchronize visible cake names (labels) and images with the selected color.
- Ensure form submission data (input values) matches the selected color.
- Prevent infinite recursion between script updates and DOM observers.
- Maintain stability and avoid duplication of gallery items.

**Non-Goals:**
- Modifying the third-party gallery plugin core code.

## Decisions

### 1. Variant-Selection-First Strategy
- **Decision**: Instead of manually swapping `textContent` and `src`, the script will programmatically `click()` the hidden radio button variant matching the selected color (e.g., `input[value*="Ivory"]`).
- **Rationale**: This leverages the gallery plugin's native synchronization logic, ensuring that images, labels, and form values are updated consistently without breaking carousel functionality or creating duplicates.

### 2. Manual Fallback for Legacy/Single Variants
- **Decision**: Maintain a fallback loop for cakes that do not have multiple radio variants (single-variant products).
- **Rationale**: Ensures the color-switch logic still applies to all products on the page, even those not following the standard WooCommerce variable product pattern.

### 3. Asynchronous Synchronization Lock
- **Decision**: Use an `isSyncing` boolean flag paired with a `setTimeout` delay (200ms) to release the lock.
- **Rationale**: Synchronous DOM updates triggered by `click()` events often cause the `MutationObserver` to fire immediately. An asynchronous lock prevents these secondary events from triggering recursive synchronization loops, which was the cause of the page freezing.

### 4. Bidirectional Syncing
- **Decision**: Implement a global `change` listener and a `MutationObserver` to ensure that if a user selects a variant manually (or via modal arrows), the global "White/Ivory" toggle updates to match.
- **Rationale**: Maintains a single "Source of Truth" for the current color mode across the entire page.

## Risks / Trade-offs

- **[Risk] Plugin Event Interference** → Programmatic clicks might trigger unexpected plugin behavior.
    - **Mitigation**: The `isSyncing` lock ensures we only process one intent at a time and ignore secondary plugin-generated events.
- **[Risk] Sync Latency** → The 200ms lock might cause a tiny delay if a user toggles very rapidly.
    - **Mitigation**: This is necessary for stability and is imperceptible to normal users.
