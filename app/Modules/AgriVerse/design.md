# AgriVerse Design System

## 1. Design Tokens

### 1.1 Core Palette

| Token | Value | Usage |
|-------|-------|-------|
| `--ag-primary-500` | `#486730` | Primary brand color, buttons, links |
| `--ag-primary-600` | `#314e1b` | Primary hover, dark variant |
| `--ag-secondary-500` | `#8b4f27` | Secondary accent (terracotta) |
| `--ag-bg` | `#fcf9f8` | Page background |
| `--ag-bg-card` | `#ffffff` | Card/surface background |
| `--ag-bg-sand` | `#f4f1ea` | Sand tone for alternate sections |
| `--ag-text-primary` | `#1b1c1c` | Body/heading text |
| `--ag-text-secondary` | `#5f6358` | Secondary/muted text (WCAG AA ≥4.5:1 on page bg) |
| `--ag-text-muted` | `#c4c8ba` | Placeholder, disabled text |
| `--ag-accent-500` | `#65625c` | Muted accent, footer copyright (≥4.7:1 on footer bg) |
| `--ag-accent-600` | `#74796c` | Outline, tertiary container (WCAG AA ≥4.5:1 on white) |
| `--ag-outline` | `var(--ag-accent-600)` | Interactive element borders |
| `--ag-border` | `#eae7e7` | Default border color |

### 1.2 Semantic Colors

| Token | Value | Usage |
|-------|-------|-------|
| `--ag-success` | `#486730` | Success state |
| `--ag-warning` | `#D97706` | Warning state |
| `--ag-danger` | `#ba1a1a` | Error/danger state |
| `--ag-info` | `#0E7490` | Info state |

### 1.3 Shadows

| Token | Value |
|-------|-------|
| `--ag-shadow-sm` | `0 1px 2px rgb(72 103 48 / 0.04)` |
| `--ag-shadow-md` | `0 4px 12px -2px rgb(72 103 48 / 0.06)` |
| `--ag-shadow-lg` | `0 10px 30px -8px rgb(44 44 44 / 0.05)` |
| `--ag-shadow-xl` | `0 24px 48px -12px rgb(44 44 44 / 0.08)` |
| `--ag-shadow-glow` | `0 0 24px -4px rgb(72 103 48 / 0.18)` |

### 1.4 Border Radius

| Token | Value |
|-------|-------|
| `--ag-radius-sm` | `0.25rem` |
| `--ag-radius-md` | `0.5rem` |
| `--ag-radius-lg` | `0.75rem` |
| `--ag-radius-xl` | `1rem` |
| `--ag-radius-2xl` | `1.5rem` |
| `--ag-radius-full` | `9999px` |

### 1.5 Typography

| Token | Value |
|-------|-------|
| Body font | `'Roboto', sans-serif` |
| Display font | `'Roboto', sans-serif` |
| Mono font | `'JetBrains Mono', monospace` |
| Base size | `16px` |
| Base line-height | `24px` |
| Font weight base | `400` |
| Font weight heading | `500` |

Heading sizes:
- `h1`: `48px / 56px`
- `h2`: `32px / 40px`
- `h3`: `24px / 32px`
- Fluid clamp scale for responsive text (xs to 4xl)

### 1.6 Transitions

| Token | Value |
|-------|-------|
| `--ag-transition-fast` | `150ms cubic-bezier(0.4, 0, 0.2, 1)` |
| `--ag-transition-base` | `250ms cubic-bezier(0.4, 0, 0.2, 1)` |
| `--ag-transition-slow` | `400ms cubic-bezier(0.4, 0, 0.2, 1)` |
| `--ag-transition-bounce` | `400ms cubic-bezier(0.16, 1, 0.3, 1)` |

### 1.7 Spacing

| Token | Value |
|-------|-------|
| Grid unit | `8px` |
| Container max | `1280px` |
| Gutter | `24px` |
| Margin desktop | `64px` |
| Margin mobile | `20px` |

---

## 2. Design Standards & Rules

### 2.1 Color Contrast (WCAG AA)

- **Body text**: Minimum contrast ratio **4.5:1** against background
- **Large text (≥18px bold / ≥24px)**: Minimum **3:1**
- **On colored backgrounds**: Always use a darker shade of the background color or white/near-white — NEVER use gray tones (e.g., `text-stone-500` on `bg-emerald-50`)
- **Status badges**: Text color must be a darker shade of the background hue:
  - `bg-emerald-50` → `text-emerald-700` (NOT stone-500)
  - `bg-amber-50` → `text-amber-600`
  - `bg-stone-100` → `text-stone-600` (NOT stone-500)

### 2.2 Borders & Accents

- **Side borders**: Max `2px` width. No thick `4px` side borders on cards or toasts
- **Rounded cards with borders**: No thick accent borders (>=3px). If rounding exists, border width must be ≤2px
- **Card borders**: Default `1px solid var(--ag-border)`. On hover, use `color-mix()` for subtle tinting
- **Toast notifications**: Use `border-left: 2px` instead of `4px` for status indicators

### 2.3 Animations & Motion

- **Easing**: Use `cubic-bezier(0.4, 0, 0.2, 1)` (standard) or `cubic-bezier(0.16, 1, 0.3, 1)` (expressive entrance)
- **NO bounce/elastic easing**: Never use `cubic-bezier(0.34, 1.56, 0.64, 1)` or `cubic-bezier(0.175, 0.885, 0.32, 1.275)`
- **Layout animation**: Animate `transform` and `opacity` only — NEVER animate `width`, `height`, `padding`, or `margin`
- **Progress bars / stat fills**: Use `transform: scaleX()` with `transform-origin: left` instead of `width`. Use `transition: transform` instead of `transition: width`
- **Chart bars**: Use `transform: scaleY()` with `transform-origin: bottom` instead of `height`
- **Micro-animations**: Fade in (0.4s ease), slide up (0.4s expo), scale in (0.35s expo)

### 2.4 Cards

- **NO nested cards**: Cards must not be placed inside other cards. Use spacing, typography, and dividers instead
- **Card variants**: `.ag-card` (bordered), `.ag-card-elevated` (shadow-based)
- **Product card**: Hover lifts 4px with shadow and image zoom

### 2.5 Typography

- **Font choices**: Avoid overused AI fonts (Inter, Roboto, Fraunces, Geist, Plus Jakarta Sans, Space Grotesk, Arial used alone)
- **Heading hierarchy**: Must not skip levels (h1 → h2 → h3 → h4). Screen reader navigation requires sequential heading levels
- **Badge/section labels**: Use `h2` for section titles, then `h3` for subsection items. Never skip from `h2` to `h4` — always insert `h3` between them
- **Uppercase text**: Reserve for short labels/badges under 20 chars. Never use uppercase on body text or long labels (>20 chars). Badges like "Bộ sưu tập đặc biệt: Bonsai Việt" (32 chars) must NOT use text-transform: uppercase
- **NO numbered section markers** (01, 02, 03 style) — use descriptive headings instead
- **Label caps**: `.ag-label-caps` for uppercase labels (14px, 600 weight, 0.05em letter-spacing)

### 2.6 Backgrounds

- **NO cream/beige defaults**: `#f4f1e8` or similar warm off-white backgrounds should come from a deliberate palette decision, not AI default
- Use `var(--ag-bg)` (#fcf9f8) as page background
- Use `var(--ag-bg-card)` (#ffffff) for surfaces

### 2.7 Padding & Spacing

- **Minimum padding in containers**: `12–16px` for bordered/colored containers, never less than `8px`
- **Forms**: Input padding `0.75rem 1rem`, button padding varies by size (sm: `0.5rem 1rem`, md: `0.625rem 1.5rem`)

### 2.8 Glassmorphism / Backdrop-Filter

- **Hero stats cards**: Background `rgba(255,255,255,0.92)` + `backdrop-filter: blur(16px)` for legibility. Opacity must not drop below 0.9 to ensure sufficient contrast for dynamic text colors (e.g., AQI color values)
- **Text in glass panels**: Never rely on `opacity` alone for text contrast. Use explicit `color` tokens (`--ag-text-primary`, `--ag-text-secondary`) instead
- **Dynamic colors**: When text color comes from dynamic data (e.g., air quality index), ensure card background is opaque enough (≥0.9 opacity) to maintain minimum 3:1 contrast ratio

### 2.9 Icon Containers

- **Minimum padding**: Icon wrappers (`.category-icon`, `.commitment-feature-icon`) must have at least `8px` inset padding, even when using flexbox centering
- **Size**: 56×56px for section icons, 48×48px for inline icons. Padding applies inside these dimensions

### 2.10 Overflow & Clipping

- **Overflow containers**: `overflow: hidden` used on hero sections, image cards, and decorative containers must not clip positioned tooltips, dropdowns, or focus indicators
- **Hero sections**: `overflow: hidden` is acceptable for clipping background media (`position: absolute` children). Do not place interactive elements (menus, tooltips) inside such containers
- **Image cards**: `overflow: hidden` for hover zoom effects is acceptable. Ensure overlay/badge elements are fully contained

---

## 3. Component Specifications

### 3.1 Buttons (`.ag-btn`)

| Variant | Background | Text | Border |
|---------|-----------|------|--------|
| Primary | `--ag-primary-500` | White | None |
| Secondary | Transparent | `--ag-text-primary` | `1.5px solid --ag-border` |
| Ghost | Transparent | `--ag-text-secondary` | None |
| Danger | `--ag-danger` | White | None |

Sizes: `sm` (8px/16px), `md` (10px/24px), `lg` (14px/32px), `xl` (16px/40px)

### 3.2 Badges (`.ag-badge`)

- `6px` dot indicator via `::before` pseudo-element
- Rounded full, padding `4px 12px`, font-size xs (600 weight)

Standard status colors (Tailwind classes):
- Pending: `bg-amber-50 text-amber-700`
- Confirmed: `bg-blue-50 text-blue-600`
- Processing: `bg-blue-50 text-blue-600`
- Shipping: `bg-sky-50 text-sky-600`
- Delivered/Approved/Active: `bg-emerald-50 text-emerald-700`
- Cancelled/Rejected: `bg-red-50 text-red-600`
- Refunded: `bg-purple-50 text-purple-600`
- Draft/Inactive: `bg-stone-100 text-stone-600`
- Suspended/Failed: `bg-red-50 text-red-600`

Inline style badges (admin status):
- Active: `bg-emerald-50 text-emerald-700`
- Inactive/Archived: `bg-stone-100 text-stone-600`

### 3.3 Forms

- Input border: `1.5px solid var(--ag-border)`
- Focus: `border-color: var(--ag-primary-500)` + `box-shadow` ring
- Border radius: `var(--ag-radius-lg)` (0.75rem)
- Label: 14px, 600 weight, margin-bottom 6px

### 3.4 Modals

- Overlay: 40% black + blur(4px)
- Content: max-width `28rem`, rounded `2xl`, scale-in entrance
- Header, body, footer sections with proper padding

### 3.5 Tables

- Header: uppercase, 12px, 600 weight, `--ag-text-secondary`
- Cells: 14px, padding `14px 16px`
- Row hover: `3% primary tint`
- Last row: no bottom border

---

### 3.6 Progress Bars

- Container: rounded-full with overflow-hidden, background `var(--ag-surface-container-high)`
- Fill element: `height: 100%; border-radius: 9999px; transform-origin: left`
- Animate via `transform: scaleX()` — NEVER `width`
- Transition: `transition: transform 0.5s ease` (or appropriate duration)
- Variants: primary (var(--ag-primary)), secondary (var(--ag-secondary)), warning, error

---

## 4. Accessibility

- All interactive elements must be keyboard-focusable
- Color contrast must meet WCAG AA minimum (4.5:1 for body, 3:1 for large text)
- Focus indicators: visible outline or ring on focus-visible
- Heading hierarchy must be sequential (no skipping levels)
- Form inputs must have associated labels
- Icon-only buttons must have `title` or `aria-label`

---

## 5. Admin Layout

- Sidebar: 224px width, card background, right border
- Content area: flex-1 with responsive padding
- Header: sticky, glass effect on scroll (blur + semi-transparent bg)
- Table filters: inline with search input and dropdown select
- Pagination: bordered page links, active state uses `--ag-primary-500`

### 5.1 Inline Status Badges (Admin Tables)

- Font-size: `10px`, padding `1.5px 6px`, rounded-full, font-semibold
- Active/Published/Completed: `bg-emerald-50 text-emerald-700`
- Inactive/Draft/Pending: `bg-amber-50 text-amber-700`
- Suspended/Cancelled: `bg-red-50 text-red-600`
- Default/Archived/Expired: `bg-stone-100 text-stone-600`

### 5.2 Chart Bars (Admin Dashboard)

- Container: `flex items-end h-32`, bars rendered inside `flex-1 flex-col` wrappers
- Bar element: `w-full h-full min-h-[4px] rounded-t-md bg-[var(--ag-primary-500)]`
- Animate via `transform: scaleY()` with `transform-origin: bottom`
- Transition: `transition-transform duration-300`
