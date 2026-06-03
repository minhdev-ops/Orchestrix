# Design System: Orchestrix Quantum Auth

## 1. Visual Theme & Atmosphere
A "Cockpit Dense" (Density 8), high-fidelity technical interface. The atmosphere is **"Quantum Void"** — a deep Zinc-950 canvas with vibrant **Electric Cyan** offsets. It uses asymmetric layouts (Variance 7) and perpetual micro-motion (Motion 8) to create a cinematic, technical feeling. This is a design built for long-term relevance by avoiding temporary trends and focusing on technical precision.

## 2. Color Palette & Roles
- **Quantum Void** (#09090B) — Primary background (Canvas)
- **Deep Surface** (#121214) — Card fill and container surfaces
- **Electric Cyan** (#4CD7F6) — Primary accent for CTAs, active states, and focal points
- **Ghost Border** (rgba(76, 215, 246, 0.15)) — 1px structural lines for glassmorphism
- **Steel Mist** (#71717A) — Secondary text and technical metadata
- **Pure White** (#FAFAFA) — Primary headline color
- **Status Amber** (#F59E0B) — Warnings and system alerts
(Saturation kept below 80% for premium feel. No purple/neon.)

## 3. Typography Rules
- **Display:** `Outfit` — Track-tight, heavy weights (900). Weight-driven hierarchy.
- **Body:** `Satoshi` or `Plus Jakarta Sans` — Relaxed leading (1.6), max 65ch width.
- **Mono:** `JetBrains Mono` — Mandatory for all technical IDs, metadata, and input text.
- **Banned:** `Inter`, generic system fonts, and any serif fonts.

## 4. Component Stylings
- **Buttons:** Sharp 2px rounded corners. High-gloss "Glass" effect. No outer glows. Tactile -1px Y-axis translate on click.
- **Inputs:** `JetBrains Mono` text. Minimalist field with active focus ring in `Electric Cyan`. Label always above.
- **Cards:** Glassmorphism with `backdrop-blur-3xl`. 1px `Ghost Border` with a linear-gradient top highlight.
- **Loaders:** Kinetic monospaced "AUTH_SEQUENCE" streaming text.
- **Social Buttons:** Minimalist monochrome icons (Google/Facebook) that gain color or brightness on hover.

## 5. Layout Principles
- **Asymmetric Grid:** Split layouts for Auth pages (Technical visuals on one side, form on the other).
- **Technical Density:** Visible 5% opacity dot grids and margin metadata (e.g., `ENCRYPTION_LAYER: TLS_1.3`).
- **Mobile Collapse:** Strict single-column stack below 768px.
- **Full Height:** `min-h-[100dvh]` to avoid iOS Safari viewport jumps.

## 6. Motion & Interaction
- **Spring Physics:** `stiffness: 120, damping: 25` for all movements.
- **Perpetual Loops:** Subtle shifting mesh gradient and "pulsing" dot grid in background.
- **Cascades:** Staggered entrance (0.05s delay) for each form field.
- **Feedback:** Shimmer effect on primary CTA during "Authorize" sequence.

## 7. Anti-Patterns (Banned)
- No emojis.
- No `Inter` font.
- No pure black (`#000000`).
- No generic shadows (use borders).
- No oversaturated gradients.
- No AI clichés ("Elevate", "Seamless").
- No circular spinners.
- No placeholder "John Doe" data.
