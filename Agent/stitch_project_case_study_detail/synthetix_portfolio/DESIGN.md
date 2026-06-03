# Design System Specification: High-End Engineering Portfolio

## 1. Overview & Creative North Star: "The Kinetic Architect"
The Creative North Star for this design system is **"The Kinetic Architect."** In the realm of Software Engineering and DevOps, code is not static—it is a living infrastructure. This system moves away from the "flat web" by treating the UI as a high-tech terminal. We utilize intentional asymmetry, overlapping layers, and light-refraction techniques to simulate a physical, illuminated workspace.

We break the "template" look by ignoring traditional 12-column rigid grids in favor of **Bento-style modularity**. Depth is not achieved through lines, but through the play of light (glows) and density (glassmorphism).

---

## 2. Colors & Surface Philosophy

### The Tonal Palette
The palette is rooted in `surface` (#131315) to provide a "true dark" experience that reduces eye strain while allowing accent colors to "pop" as if they are light-emitting diodes.

- **Primary (The Pulse):** `primary` (#4cd7f6) / `primary_container` (#06b6d4). Used for active states and critical paths.
- **Secondary (The Logic):** `secondary` (#c0c1ff) / `secondary_container` (#3131c0). Used for DevOps-specific metrics or secondary navigation.
- **Tertiary (The Alert):** `tertiary` (#ffb873). Reserved for warnings or build-failure states.

### The "No-Line" Rule
**Borders are an admission of failure in spatial design.** You are prohibited from using 1px solid borders to define sections. Instead:
- **Tonal Shifts:** Use `surface_container_low` for the main background and `surface_container_highest` for nested cards.
- **Edge Definition:** Boundaries must be defined by the transition between two surface tokens.

### Surface Hierarchy & Nesting
Treat the UI as stacked layers of obsidian and frosted glass:
1.  **Base Layer:** `surface` (The void).
2.  **Section Layer:** `surface_container_low` (Subtle grouping).
3.  **Component Layer:** `surface_container_high` (Bento cards, timeline nodes).
4.  **Interaction Layer:** `surface_bright` (Hover states).

### The "Glass & Gradient" Rule
For floating elements like search bars or navigation docks, use `surface_variant` at 40% opacity with a `24px` backdrop-blur. Apply a linear gradient (45deg) from `primary` to `secondary` at 10% opacity as a subtle "sheen" on the glass surface.

---

## 3. Typography: Editorial Engineering
We pair the utilitarian **Inter** (Sans-Serif) with **Space Grotesk** (Monospace-adjacent) for labels to evoke the feel of a high-end technical manual.

*   **Display (The Statement):** `display-lg` (3.5rem). Use for hero headlines. Tight letter-spacing (-0.02em) to create an authoritative, "editorial" feel.
*   **Headlines (The Architecture):** `headline-md` (1.75rem). Used for section titles. Pair with a `primary` color underline or glow.
*   **Body (The Documentation):** `body-md` (0.875rem). High legibility for technical descriptions.
*   **Labels (The Metadata):** `label-md` (0.75rem, Space Grotesk). Used for tags, timestamps, and "DevOps" metrics.

---

## 4. Elevation & Depth: Atmospheric Lighting

### The Layering Principle
Depth is achieved by "stacking" tones. A card (`surface_container_highest`) sitting on a section (`surface_container_low`) creates a natural lift.

### Ambient Shadows
Avoid black shadows. Use "Luminous Shadows":
- **Blur:** 40px - 60px.
- **Color:** `primary` or `secondary` at 5% opacity.
- **Effect:** This mimics the glow of a monitor in a dark room rather than a physical object casting a shadow.

### The "Ghost Border" Fallback
If a boundary is required for accessibility (e.g., input fields), use `outline_variant` at **15% opacity**. This creates a "whisper" of a container without breaking the minimalist aesthetic.

### Signature Component: The Glowing Edge
For active states (e.g., a selected project card), apply a 1px inner-stroke using a gradient from `primary` to `secondary`. Add an outer `box-shadow` of the same colors at 20% opacity to simulate an "active power" state.

---

## 5. Components

### Bento Cards
- **Structure:** No dividers. Separate content using `body-lg` for headers and `label-md` for metadata.
- **Padding:** 24px (1.5rem) consistent internal padding.
- **Radius:** Use `xl` (0.75rem) for the outer container and `lg` (0.5rem) for internal nested elements.

### Glassmorphism Search Bar
- **Surface:** `surface_container_highest` at 60% opacity.
- **Blur:** 16px backdrop-blur.
- **Interaction:** On focus, the `outline` token pulses from 20% to 50% opacity.

### Vertical Timeline (The DevOps Thread)
- **Line:** 2px wide, using `surface_container_highest`. No solid lines.
- **Nodes:** Use `primary` for "Current" and `outline_variant` for "Past."
- **Content:** Timeline events are cards using `surface_container_low`.

### Buttons
- **Primary:** Gradient from `primary_container` to `primary`. Text: `on_primary`. No border.
- **Secondary:** Transparent background, `outline_variant` (20% opacity) border. Text: `on_surface`.
- **Tertiary:** Text only, `primary` color. 

### Inputs & Fields
- **Background:** `surface_container_lowest`.
- **Active State:** A subtle `primary` glow at the bottom 2px of the input—never a full high-contrast box.

---

## 6. Do's and Don'ts

### Do:
- **Do** use asymmetrical spacing. A wider gap on the left of a Bento grid can create a sophisticated, intentional "dead space."
- **Do** use `Space Grotesk` for all technical data (e.g., "Build: Success", "Latency: 24ms").
- **Do** use `primary_fixed_dim` for text links within body copy to ensure contrast.

### Don't:
- **Don't** use 100% white (#FFFFFF) for text. Use `on_surface` (#e5e1e4) to maintain the dark-mode's tonal depth.
- **Don't** use "Drop Shadows." Only use "Ambient Glows."
- **Don't** use divider lines. If you need to separate two items, use a `1.5rem` vertical spacer or a subtle background shift to `surface_container_high`.
- **Don't** use standard "Blue" links. Every interactive element must use the defined `primary` (Cyan) or `secondary` (Indigo) accents.