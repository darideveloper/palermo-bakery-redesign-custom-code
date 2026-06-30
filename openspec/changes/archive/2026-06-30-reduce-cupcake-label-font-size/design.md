## Context

The `.toothpick-flag` element currently has `font-size: 12px`. Client wants it reduced to `10px` for a more proportional look within the small flag area. This is a single CSS property change — no structural or behavioral impact.

## Goals / Non-Goals

**Goals:**
- Set `.toothpick-flag` font-size to `10px`

**Non-Goals:**
- No changes to layout, padding, or positioning
- No changes to JS or HTML
- No spec-level requirement changes

## Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Target selector | `.toothpick-flag` | Single source of truth for the label styling |
| Value | `10px` | Client request; fits proportionally within the flag |

## Risks / Trade-offs

- None significant. Readability may be slightly reduced at smaller sizes, but 10px on a bold label is still legible.
