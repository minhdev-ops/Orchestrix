# Design System: Orchestration & Technical Intelligence

## 1. Overview & Creative North Star: "The Obsidian Conductor"
This design system is built for the high-stakes environment of systems orchestration. The Creative North Star is **"The Obsidian Conductor"**—an aesthetic that mirrors a high-end command center. It rejects the "Bootstrap-flat" look in favor of a cinematic, deep-field experience. 

We move beyond standard dashboards by utilizing a **Bento Grid** logic that prioritizes information density without visual clutter. By breaking the rigid "box-on-box" layout with intentional asymmetry, varying glass opacities, and high-contrast technical typography, we create an interface that feels like a precision instrument. This is not just a tool; it is a professional-grade cockpit for engineering excellence.

---

## 2. Colors & Surface Philosophy
The palette is rooted in a "Deep Navy" foundation, utilizing Material Design tokens to create a sophisticated, low-light environment that reduces eye strain during long engineering sprints.

### The "No-Line" Rule
**Borders are a design failure.** In this system, 1px solid borders for sectioning are strictly prohibited. Boundaries must be defined solely through:
- **Tonal Shifts:** Placing a `surface-container-high` card against a `surface` background.
- **Glass Refraction:** Using `backdrop-filter: blur()` to create a physical sense of separation.
- **Negative Space:** Relying on the Spacing Scale to let the eye define groups.

### Surface Hierarchy & Nesting
Treat the UI as a series of physical layers. We use "Tonal Layering" to establish importance:
- **Base Level:** `surface` (#0b1326) – The deep navy canvas.
- **Sectioning:** `surface-container-low` (#131b2e) – Subtle regions within the dashboard.
- **Primary Data Cards:** `surface-container-highest` (#2d3449) – These are your "Bento" blocks.
- **The "Glass & Gradient" Rule:** Floating elements (modals, tooltips, popovers) must use a semi-transparent version of `surface-bright` with a `20px` backdrop blur. 
- **Signature Textures:** For high-value actions, use a linear gradient from `primary` (#c0c1ff) to `primary-container` (#4b4dd8) at a 135-degree angle to provide a "metallic" technical sheen.

---

### 3. Typography: The Technical Editorial
The system pairs the brutalist, geometric rhythm of **Space Grotesk** with the neutral clarity of **Inter**.

| Level | Token | Font | Size | Weight | Use Case |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Display** | `display-lg` | Space Grotesk | 3.5rem | 700 | Massive KPI numbers, Hero stats |
| **Headline** | `headline-md` | Space Grotesk | 1.75rem | 500 | Main Module Headers |
| **Title** | `title-sm` | Inter | 1rem | 600 | Card titles, Section labels |
| **Body** | `body-md` | Inter | 0.875rem | 400 | Data tables, Logs, Descriptions |
| **Label** | `label-sm` | Inter | 0.6875rem | 700 | All-caps, tracked-out metadata |

**Editorial Note:** Use `label-sm` with a `0.05em` letter-spacing for technical metadata (e.g., TIMESTAMP, NODE_ID) to create an authoritative, "monospaced-adjacent" feel without sacrificing Inter’s legibility.

---

## 4. Elevation & Depth
In a dark, data-heavy environment, traditional shadows feel "muddy." We use **Ambient Luminous Depth**.

*   **The Layering Principle:** Depth is achieved by "stacking." A `surface-container-lowest` card (#060e20) "recedes" into the dashboard, while a `surface-container-highest` (#2d3449) "rises."
*   **Ambient Shadows:** For floating glass panels, use a shadow with a `40px` blur and `6%` opacity. The shadow color should be `on-surface` (#dae2fd) rather than black, creating a "glow" effect rather than a dark pit.
*   **The "Ghost Border" Fallback:** If a border is required for high-density data accessibility, use `outline-variant` (#464555) at **15% opacity**. It should be felt, not seen.
*   **Glassmorphism:** Apply a 0.5px "inner stroke" to the top and left edges of cards using `outline` (#918fa1) at 20% opacity to simulate light catching the edge of a glass pane.

---

## 5. Components

### The Bento Cards
*   **Structure:** No dividers. Use `surface-container-high` for the card body.
*   **Header:** Use `title-sm` in `secondary` (#4cd7f6) for emphasis.
*   **Padding:** Strict `1.5rem` (xl roundedness) internal padding.

### Buttons (The "Logic Gates")
*   **Primary:** Gradient fill (`primary` to `primary-container`). White text (`on-primary`). `0.25rem` (DEFAULT) roundedness for a technical, sharp look.
*   **Secondary:** Ghost style. Transparent background with the "Ghost Border" (15% `outline-variant`). Text in `secondary`.
*   **Tertiary:** Text only. `label-md` uppercase.

### Status Indicators (Vibrant Alerts)
In an orchestration context, color is data.
*   **Healthy (Emerald):** Use `tertiary` (#4edea3).
*   **Warning (Amber):** Use custom Amber (not in tokens, use a high-saturation variant of `on-secondary-fixed-variant`).
*   **Critical (Rose):** Use `error` (#ffb4ab).
*   **Styling:** Status dots should have a soft outer glow (bloom) of the same color to simulate hardware LEDs.

### Input Fields
*   **Surface:** Use `surface-container-lowest`. 
*   **State:** On focus, the "Ghost Border" becomes `secondary` (#4cd7f6) at 100% opacity. No "glow" shadows—only a sharp color shift.

---

## 6. Do's and Don'ts

### Do:
*   **Use Asymmetry:** In the Bento grid, let some cards span 2 columns and others 1. This "Editorial" layout prevents the dashboard from looking like a generic template.
*   **Layer Glass:** Use backdrop blur on top of background gradients to create "depth-of-field."
*   **Embrace High Contrast:** Use `primary` (#c0c1ff) for key interactions to make them pop against the `Deep Navy` background.

### Don't:
*   **Don't use Divider Lines:** Never use a 1px line to separate list items. Use a 4px vertical gap or a subtle toggle between `surface-container-low` and `surface-container-lowest`.
*   **Don't use Standard "Grey":** Every neutral color in this system must be tinted with Navy or Slate. Pure `#333333` is forbidden; it kills the "Obsidian" depth.
*   **Don't Round Everything:** Keep `0.25rem` for functional components (buttons, inputs) to maintain a "technical/sharp" edge, while using `0.75rem` (xl) for the structural Bento cards.