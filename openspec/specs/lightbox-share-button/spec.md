## ADDED Requirements

### Requirement: Share button appears in the lightbox
The system SHALL inject a Font Awesome share icon inside the prettyPhoto lightbox whenever it is opened, positioned to the left of the fav button at the bottom of the displayed image.

#### Scenario: Lightbox opens from gallery click
- **WHEN** a user clicks a gallery cake image and the prettyPhoto lightbox opens
- **THEN** a share button (icon) appears to the left of the fav button, centered at the bottom of the lightbox image

#### Scenario: Button does not duplicate on repeated opens
- **WHEN** a user closes and re-opens the lightbox multiple times
- **THEN** only one share button is present inside the lightbox at any time

### Requirement: Share button copies a single-cake shared-favorites URL to clipboard
The system SHALL copy a shared-favorites link (`/favorite-cakes/?shared_favs=<productId>`) to the system clipboard when the share button is clicked, providing visual confirmation of the copy action. This reuses the same URL format as the existing favorites page share feature, scoped to a single cake.

#### Scenario: User clicks share button successfully
- **WHEN** the lightbox is open and the user clicks the share button
- **THEN** a URL of the form `window.location.origin + "/favorite-cakes/?shared_favs=" + currentLightboxProductId` is copied to the clipboard and the button shows a "Link Copied!" message briefly

#### Scenario: Share button clicked when product ID is unavailable
- **WHEN** the lightbox is open but `currentLightboxProductId` is null (lightbox opened outside gallery context)
- **THEN** the share button either does nothing or shows a fallback state (no error thrown)

### Requirement: Share button state updates on lightbox navigation
The system SHALL update the share button to reference the newly displayed cake whenever the user navigates between images inside the lightbox using the prev/next arrows or keyboard.

#### Scenario: User navigates to a different cake
- **WHEN** the user clicks the next or previous arrow inside the lightbox
- **THEN** the share button now copies the newly displayed cake's shared-favorites link when clicked

#### Scenario: User navigates with keyboard arrows
- **WHEN** the user presses the left or right keyboard arrow while the lightbox is open
- **THEN** the share button updates to copy the newly displayed cake's shared-favorites link

### Requirement: Share button styling matches fav button
The system SHALL apply consistent styling to the share button including positioning, box-shadow, and hover effects that match the existing fav button design.

#### Scenario: Hover interaction
- **WHEN** the user hovers over the lightbox share button
- **THEN** the button scales up (transform: scale(1.15)) per existing CSS, matching the fav button hover behavior

#### Scenario: Share button only functional in gallery context
- **WHEN** the lightbox opens from a non-gallery context (no `.product-inner` parent)
- **THEN** the share button is not injected or is non-functional (matches existing fav button behavior)
