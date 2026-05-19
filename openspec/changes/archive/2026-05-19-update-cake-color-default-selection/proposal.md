## Why

The current method of setting the default "White" selection on the wedding cake order form (setting `.checked = true` and dispatching a `change` event) was too immediate and potentially racing with other scripts or DOM adjustments. Switching to a delayed `click()` simulation ensures the UI state and any associated logic are properly synchronized after the page has stabilized.

## What Changes

- Modified `src/features/order-cake/order-wedding-cake-change-cake-color.js` to replace immediate property assignment with a 100ms delayed `click()` call.

## Capabilities

### New Capabilities
- `wedding-cake-color-defaults`: Ensures the wedding cake order form initializes with the "White" color selected by default using a reliable delayed interaction pattern.

### Modified Capabilities
<!-- No existing specs found for the wedding cake order form color logic specifically. -->

## Impact

- `src/features/order-cake/order-wedding-cake-change-cake-color.js`
- Wedding cake order form initialization behavior.
