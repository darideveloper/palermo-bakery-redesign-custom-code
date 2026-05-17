## Context

The login and registration pages are powered by the Theme My Login (TML) plugin. Currently, these forms inherit the theme's wide content container, making the inputs feel stretched and disjointed from the compact, modern aesthetic of the rest of the gallery redesign.

## Goals / Non-Goals

**Goals:**
- Implement a focused, centered layout for authentication forms.
- Ensure consistent vertical spacing across all auth-related pages.
- Maintain a clean separation of auth-specific styles.

**Non-Goals:**
- Modifying the styling of individual form elements (inputs, labels, buttons) at this stage.
- Changing the functional flow of authentication.

## Decisions

### Decision 1: Dedicated stylesheet for auth overrides
- **Chosen**: `custom-auth.css`
- **Rationale**: Following the project convention of splitting code into logical files. This prevents `category-filter-menu.css` or `form-style.css` from becoming bloated with unrelated rules.

### Decision 2: Target the TML wrapper class
- **Chosen**: `.tml` selector
- **Rationale**: The Theme My Login plugin wraps all its output (login, register, lost password) in a container with the `.tml` class. Targeting this ensures layout consistency across all auth states without duplicating rules.

### Decision 3: Layout constraints using `max-width` and `auto` margins
- **Chosen**: `max-width: 600px` and `margin: 50px auto`
- **Rationale**: A 600px max-width is the industry standard for readable form layouts. Using `margin: auto` provides perfect horizontal centering. The `!important` flag is used because the base theme (`snsvicky`) has high-specificity rules for content containers that would otherwise override these custom layout choices.

## Risks / Trade-offs

- **Risk**: Fixed margins might create too much white space on smaller viewports.
  - **Mitigation**: Using `max-width` instead of `width` allows the container to respond to smaller screens, and standard CSS margin behavior ensures the form remains usable even when the 50px vertical margin is reduced by browser chrome on mobile.
