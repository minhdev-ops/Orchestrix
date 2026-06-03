# Design System: Orchestrix Web3 Modern

## 1. Visual Theme & Atmosphere
A high-fidelity, cinematic dark interface. It combines technical precision with artistic flair. The atmosphere is **"Quantum Void"** — a deep, mysterious Zinc-950 canvas with vibrant, calibrated Cyan offsets. High density of technical details, asymmetric layouts, and perpetual micro-interactions characterize the experience.

## 2. Color Palette & Roles
- **Quantum Void** (#09090B) — Primary background (Canvas)
- **Deep Surface** (#18181B) — Secondary background, card fill
- **Electric Cyan** (#4CD7F6) — Primary accent for CTAs, active states, and focal points
- **Steel Mist** (#71717A) — Secondary text, metadata, and muted details
- **Ghost Border** (rgba(39,39,42,0.5)) — 1px structural lines, glassmorphism borders
- **Pure White** (#FAFAFA) — Primary headline color

## 3. Typography Rules
- **Display:** `Outfit` — Track-tight, heavy weights (900/700). High contrast.
- **Body:** `Satoshi` — Relaxed leading (1.6), max 65ch width. Clean and legible.
- **Mono:** `JetBrains Mono` — For code, technical stats, timestamps, and IDs.
- **Banned:** `Inter`, generic system fonts, pure black (#000000), oversaturated purple neon.

## 4. Component Stylings
- **Buttons:** Sharp or subtly rounded (0.5rem). High-gloss primary, ghost-outline secondary. No outer glows.
- **Cards:** Flat design with subtle 1px "Ghost Border". Glassmorphism (blur-2xl) for overlays.
- **Inputs:** Minimalist bottom-border only or very subtle field. Monospaced input text.
- **Loaders:** Kinetic monospaced "Streaming" indicators instead of spinners.
- **Micro-interact:** 1px translate, scale(1.02), and subtle glow-on-hover (tinted to accent).

## 5. Layout Principles
- **Asymmetric Hero:** Split layouts with large typographic headlines and abstract technical visuals.
- **Technical Grid:** Subtle visible grid lines or dot patterns at 5% opacity.
- **Z-Space:** Use backdrop-blur-3xl for modal and navigation overlays.
- **Density:** High density of metadata (using Mono font) to convey technical expertise.

## 6. Motion & Interaction
- **Physics:** Heavy spring physics (stiffness: 120, damping: 25).
- **Streams:** Perpetual micro-animations (e.g., subtle shifting glow, scrolling text).
- **Cascades:** Staggered entrance for all list items and grid cells.

## 7. Anti-Patterns (Banned)
- No emojis.
- No `Inter` font.
- No generic card shadows (use borders or negative space).
- No "3-column equal card grid" (use asymmetric or horizontal masonry).
- No AI copywriting clichés ("Seamless", "Empower", "Next-gen").
- No pure black background (#000000).
