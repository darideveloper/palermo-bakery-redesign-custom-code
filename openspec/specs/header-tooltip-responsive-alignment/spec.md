## ADDED Requirements

### Requirement: Tooltip is right-aligned on tablet and mobile
On screens with a viewport width of 991px or less, the "Favorites Board" tooltip on the header heart icon SHALL be anchored to the right edge of the icon so it never overflows the viewport to the right.

#### Scenario: Tooltip stays within viewport on mobile
- **WHEN** a user hovers over the header heart icon on a screen ≤ 991px wide
- **THEN** the tooltip appears with its right edge aligned with the icon's right edge, fully visible within the viewport

#### Scenario: Tooltip does not overflow on tablet
- **WHEN** a user hovers over the header heart icon on a tablet screen (768px–991px)
- **THEN** the tooltip is right-aligned and no horizontal scrollbar is introduced

### Requirement: Tooltip remains centered on desktop
On screens with a viewport width of 992px or greater, the tooltip SHALL retain its existing centered horizontal alignment (centered over the icon), unchanged from the current implementation.

#### Scenario: Desktop tooltip is centered
- **WHEN** a user hovers over the header heart icon on a screen ≥ 992px wide
- **THEN** the tooltip appears horizontally centered over the icon, as before this change

### Requirement: Tooltip alignment is CSS-only
The responsive alignment adjustment SHALL be implemented exclusively via CSS media queries with no JavaScript involvement.

#### Scenario: No JS required for alignment
- **WHEN** the page loads with JavaScript disabled
- **THEN** the tooltip alignment still works correctly on all breakpoints
