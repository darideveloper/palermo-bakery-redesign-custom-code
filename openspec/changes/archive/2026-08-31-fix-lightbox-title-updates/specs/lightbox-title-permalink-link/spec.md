## MODIFIED Requirements

### Requirement: Title link updates on lightbox navigation

The system SHALL update the `.ppt` anchor's `href` AND its visible **text** to reflect the newly displayed cake whenever the user navigates between images inside the lightbox using the prev/next arrows or keyboard arrows. The anchor text resolution SHALL use the same authoritative source as the permalink — the matching `a.product-image` anchor's `title` (or its `.item-title a` text), falling back to a `productId → title` map — and SHALL NOT read the title from the previously-rendered `.ppt` text content, which becomes stale because prettyPhoto writes caption updates to a detached node after the `.ppt` is converted to an anchor.

#### Scenario: User navigates to a different cake
- **WHEN** the user clicks the next or previous arrow inside the lightbox
- **THEN** the `.ppt` anchor's text SHALL reflect the newly displayed cake's name
- **AND** the `.ppt` anchor's `href` SHALL update to reflect the newly displayed cake
- **AND** clicking the updated title SHALL open the correct product page

#### Scenario: User navigates with keyboard arrows
- **WHEN** the user presses the left or right keyboard arrow while the lightbox is open
- **THEN** the `.ppt` anchor's text SHALL reflect the newly displayed cake's name
- **AND** the `.ppt` anchor's `href` SHALL update to reflect the newly displayed cake

#### Scenario: Title text matches the currently displayed image after navigation
- **WHEN** the lightbox displays a cake after navigating away from the initially-opened cake
- **THEN** the visible `.ppt` anchor text SHALL equal the `title` of the `a.product-image` anchor whose image `src` matches the current lightbox image
- **AND** the visible `.ppt` anchor text SHALL NOT remain set to the name of the cake that was displayed before navigation

#### Scenario: Title text resolved via the productId-to-title map fallback
- **WHEN** the image-src title match fails to find a title for the current lightbox cake
- **THEN** the JS SHALL look up the current product ID in the `productId → title` map
- **AND** the resulting `.ppt` anchor text SHALL equal the looked-up title
- **AND** the map SHALL be populated on `/cake-gallery/` only, alongside the existing `productId → permalink` map, using the same anchor scan and the corresponding product's YITH `data-fragment-ref` as the key

#### Scenario: Missing title leaves the prior title text unchanged while href still updates
- **WHEN** the JS cannot resolve a title for the current lightbox cake through any path
- **THEN** the `.ppt` anchor text SHALL be left unchanged (no empty or placeholder title is emitted)
- **AND** the `.ppt` anchor's `href` SHALL still update to the resolved permalink when one is available