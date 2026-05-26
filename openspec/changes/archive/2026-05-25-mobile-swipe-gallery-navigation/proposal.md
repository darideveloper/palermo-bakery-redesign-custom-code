## Why

Users on mobile devices expect swipe gestures to navigate through image galleries. Currently, they must rely on tapping specific areas of the screen. Implementing swipe navigation makes the gallery more intuitive and improves the overall mobile user experience.

## What Changes

- Implement touch gesture detection (swipe left/right) on the lightbox modal.
- Map swipe left to "Next Image" (triggering the `.pp_next` button click).
- Map swipe right to "Previous Image" (triggering the `.pp_previous` button click).
- Add horizontal swipe sensitivity threshold to prevent accidental navigation during vertical scrolling.

## Capabilities

### New Capabilities
- `mobile-swipe-navigation`: Provides touch-based navigation (swipe left/right) for the cake gallery lightbox on mobile devices.

### Modified Capabilities
<!-- No existing spec requirements are changing -->

## Impact

- `src/features/lightbox/image-lightbox.js`: Core logic for touch event handling and button triggering.
- Mobile browsing experience: Significant UX improvement for touch users.
