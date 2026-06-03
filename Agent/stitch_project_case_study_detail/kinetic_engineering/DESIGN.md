# Design System Specification: Technical Precision & Editorial Clarity

## 1. Overview & Creative North Star
The Creative North Star for this design system is **"The Architectural Blueprint."** 

This system rejects the "boxed-in" nature of standard SaaS templates. Instead, it adopts the logic of high-end engineering schematics—relying on mathematical precision, expansive negative space, and tonal layering. We move away from the "cluttered dashboard" aesthetic toward an editorial, high-precision experience where every pixel feels calculated. By utilizing asymmetric layouts and a rigorous hierarchy, we convey a sense of technical authority and premium craftsmanship.

---

## 2. Colors & Tonal Architecture
The palette is rooted in a "cool-machined" spectrum, moving from sterile whites to deep, structural slates, punctuated by a high-energy Cyan.

### The "No-Line" Rule
To achieve a high-end feel, **1px solid borders are prohibited for structural sectioning.** Boundaries between major content areas must be defined strictly through background shifts. For example, a sidebar using `surface-container-low` should sit against a `surface` main stage without a dividing line. The eye should perceive the change in depth through color, not a stroke.

### Surface Hierarchy & Nesting
Treat the UI as a series of nested, machined plates.
- **Base Layer:** `surface` (#f8f9ff) - The canvas.
- **Sectioning:** `surface-container-low` (#eff4ff) - For grouping related data.
- **Prominence:** `surface-container-highest` (#d3e4fe) - For active navigation or highlighted panels.
- **The "Glass & Gradient" Rule:** Floating modals or navigation bars should use `surface_container_lowest` at 80% opacity with a `24px` backdrop-blur. This "frosted glass" effect prevents the UI from feeling "pasted on" and ensures it feels integrated into the environment.

### Signature Textures
Main CTAs and Hero moments should utilize a **Linear Technical Gradient**: `primary` (#00687a) to `primary_container` (#06b6d4) at a 135-degree angle. This mimics the "glow" of a high-resolution display and adds a "soul" to the otherwise sterile slate environment.

---

## 3. Typography: Geometric Authority
We utilize a dual-font strategy to balance technical rigidity with modern readability.

*   **Display & Headlines (Space Grotesk):** This is our "Engineering" voice. Its geometric construction feels like a modern blueprint. Use `display-lg` through `headline-sm` for high-impact areas. Always use "Optical" kerning and a slight letter-spacing reduction (-0.02em) for headlines to increase density and authority.
*   **Body & UI (Inter):** Our "Functional" voice. Inter provides maximum legibility for complex data. Use `body-md` for standard prose and `label-sm` for technical metadata.
*   **The Editorial Scale:** Break the grid by pairing a massive `display-lg` headline with a very small, wide-tracked `label-md` in all caps. This contrast is the hallmark of premium design.

---

## 4. Elevation & Depth
In this system, depth is a function of light and material, not artificial shadows.

*   **The Layering Principle:** Avoid shadows for static cards. Instead, place a `surface-container-lowest` (#ffffff) card on top of a `surface-container-low` (#eff4ff) background. This creates a "soft lift" that feels natural and high-end.
*   **Ambient Shadows:** If an element must float (e.g., a dropdown), use an **Ambient Light Shadow**: `0px 20px 40px rgba(11, 28, 48, 0.06)`. The tint is derived from `on_surface`, making it feel like a true occlusion of light.
*   **The "Ghost Border" Fallback:** If a container requires definition against a similar background (for accessibility), use a **Ghost Border**: `outline-variant` (#bcc9cd) at **15% opacity**. Never use a 100% opaque border for containment.

---

## 5. Components

### Buttons
*   **Primary:** Technical Gradient (Primary to Primary Container) with `on_primary` text. `radius-md` (0.375rem). No shadow.
*   **Secondary:** `surface-container-highest` background with `primary` text.
*   **Tertiary:** Ghost style; text-only with `primary` color. On hover, a subtle `surface-container-low` background appears.

### Input Fields
*   **State:** Background should be `surface-container-lowest`. 
*   **Focus:** Transition the "Ghost Border" to 100% opacity `primary` (#00687a) and add a 2px outer "glow" using `primary_fixed_dim` at 30% opacity.
*   **Precision:** Labels use `label-md` in `on_surface_variant`.

### Cards & Lists
*   **The Divider Ban:** Vertical lines are forbidden. Separate list items using 12px of vertical white space or alternating subtle background shifts (`surface` vs `surface-container-low`). 
*   **Data Density:** For engineering contexts, use `body-sm` for data tables to maximize information density while maintaining "breathable" cell padding (16px).

### Chips (Action & Filter)
*   Used for status and filtering. Use `secondary_container` with `on_secondary_container` text. Shapes must be `full` (pill) for status and `sm` (0.125rem) for technical tags to differentiate "State" from "Metadata."

---

## 6. Do’s and Don’ts

### Do
*   **Do** use asymmetrical white space. Leave one side of a layout significantly more open than the other to create an editorial feel.
*   **Do** use `primary_container` (#06b6d4) sparingly as a "laser pointer" to guide the eye to the most critical action.
*   **Do** align all text to a rigorous 4px baseline grid to maintain "Engineering" precision.

### Don’t
*   **Don’t** use pure black (#000000) for text. Use `on_surface` (#0b1c30) to maintain tonal harmony with the slate tones.
*   **Don’t** use "standard" 16px border-radii. Stick to the `md` (0.375rem) or `sm` (0.125rem) values to keep the "precision-tooled" look. Large rounds feel too "consumer" and "soft."
*   **Don’t** use heavy drop shadows. If it looks like it’s "popping off" the page, the shadow is too dark. It should look like it’s barely hovering.