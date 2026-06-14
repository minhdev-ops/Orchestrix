---
name: Botanical Heritage
colors:
  surface: '#fcf9f8'
  surface-dim: '#dcd9d9'
  surface-bright: '#fcf9f8'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f6f3f2'
  surface-container: '#f0eded'
  surface-container-high: '#eae7e7'
  surface-container-highest: '#e4e2e1'
  on-surface: '#1b1c1c'
  on-surface-variant: '#43483d'
  inverse-surface: '#303030'
  inverse-on-surface: '#f3f0f0'
  outline: '#74796c'
  outline-variant: '#c4c8ba'
  surface-tint: '#486730'
  primary: '#486730'
  on-primary: '#ffffff'
  primary-container: '#87a96b'
  on-primary-container: '#213d0b'
  inverse-primary: '#aed18f'
  secondary: '#8b4f27'
  on-secondary: '#ffffff'
  secondary-container: '#fdaf7e'
  on-secondary-container: '#784019'
  tertiary: '#5f5e59'
  on-tertiary: '#ffffff'
  tertiary-container: '#a19f99'
  on-tertiary-container: '#373632'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#c9eea9'
  primary-fixed-dim: '#aed18f'
  on-primary-fixed: '#0b2000'
  on-primary-fixed-variant: '#314e1b'
  secondary-fixed: '#ffdbc8'
  secondary-fixed-dim: '#ffb68a'
  on-secondary-fixed: '#321300'
  on-secondary-fixed-variant: '#6e3811'
  tertiary-fixed: '#e5e2db'
  tertiary-fixed-dim: '#c9c6c0'
  on-tertiary-fixed: '#1c1c18'
  on-tertiary-fixed-variant: '#474742'
  background: '#fcf9f8'
  on-background: '#1b1c1c'
  surface-variant: '#e4e2e1'
typography:
  display-lg:
    fontFamily: EB Garamond
    fontSize: 48px
    fontWeight: '500'
    lineHeight: 56px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: EB Garamond
    fontSize: 36px
    fontWeight: '500'
    lineHeight: 42px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: EB Garamond
    fontSize: 32px
    fontWeight: '500'
    lineHeight: 40px
  headline-sm:
    fontFamily: EB Garamond
    fontSize: 24px
    fontWeight: '500'
    lineHeight: 32px
  body-lg:
    fontFamily: Hanken Grotesk
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Hanken Grotesk
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-md:
    fontFamily: Hanken Grotesk
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.05em
  caption:
    fontFamily: Hanken Grotesk
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  container-max: 1280px
  gutter: 24px
  margin-desktop: 64px
  margin-mobile: 20px
---

## Brand & Style

This design system embodies a "Green Tech" aesthetic—a synthesis of high-end horticulture and precise digital craftsmanship. The brand personality is scholarly yet accessible, positioning the product as both a luxury curator and a scientific authority on plant care. 

The visual style is **Elevated Minimalism** with **Organic Inflections**. It prioritizes high-quality botanical photography by using expansive whitespace and a restrained UI that recedes to let the organic textures of the plants lead. The emotional response should be one of tranquility, confidence, and environmental stewardship. Elements are grounded in a structured grid but softened by organic radii and subtle, natural depth to avoid a sterile corporate feel.

## Colors

The palette is derived from natural earth pigments and forest canopies. 

*   **Primary (Sage Green):** Used for growth-oriented actions, primary buttons, and brand markers.
*   **Secondary (Terracotta):** An accent color used sparingly for "Sale" indicators, notification badges, or to highlight earth-related data points (e.g., soil pH).
*   **Tertiary (Sand/Bone):** The foundational surface color. It replaces pure white in many areas to provide a softer, more organic reading experience.
*   **Neutral (Charcoal/Ink):** Used for high-contrast typography and structural borders. 

Backgrounds should primarily utilize the Tertiary (#F4F1EA) for a premium, paper-like feel, with the Primary used as a subtle wash in specific sections.

## Typography

The typographic hierarchy relies on the contrast between the intellectual, historical character of **EB Garamond** and the modern, technical precision of **Hanken Grotesk**.

**EB Garamond** is reserved for editorial headings, product names, and pull quotes. It should be typeset with slightly tighter tracking in display sizes to emphasize its elegant ligatures.

**Hanken Grotesk** handles all functional UI, descriptions, and data. Labels use an uppercase treatment with increased letter spacing to provide a clear, "engineered" look for technical specs like light requirements and watering frequency.

## Layout & Spacing

The layout utilizes a **Fixed-Fluid Hybrid** model. Content is contained within a 1280px central column on desktop to maintain readability, while photography-heavy sections may break the grid to bleed to the edge of the viewport.

*   **Rhythm:** An 8px base grid drives all padding and margin decisions. 
*   **Density:** Generous "breathability" is a core requirement. Padding within cards and containers should lean towards larger increments (32px+) to signify luxury.
*   **Desktop:** 12-column grid with 24px gutters.
*   **Mobile:** 4-column grid with 16px gutters and 20px side margins. 

Vertical spacing between sections should be significant (80px to 120px) to separate the different "stories" of the botanical collection.

## Elevation & Depth

This design system avoids heavy drop shadows in favor of **Tonal Layering** and **Soft Ambient Occlusion**.

*   **Surface Hierarchy:** Level 0 is the Sand background (#F4F1EA). Level 1 (Cards/Modals) uses pure White (#FFFFFF).
*   **Shadows:** When used, shadows must be extremely diffused with a low opacity (e.g., `box-shadow: 0 10px 30px rgba(44, 44, 44, 0.05)`). The shadow color should be a dark tint of the primary green rather than pure black to keep the depth feeling natural and "outdoor-lit."
*   **Glassmorphism:** Use subtle backdrop blurs (8px-12px) for sticky navigation bars to maintain a sense of space while scrolling over vibrant plant photography.

## Shapes

The shape language is "Softly Architectural." We avoid the clinical nature of sharp corners and the playfulness of full pills.

*   **Containers & Cards:** Use a consistent 0.5rem (8px) radius.
*   **Interactive Elements:** Buttons and input fields follow the same 8px radius to ensure a cohesive visual unit.
*   **Media:** High-quality photography may occasionally use a larger radius (1.5rem) or even asymmetrical rounded corners (e.g., top-left and bottom-right) to mimic the organic asymmetry of leaves.

## Components

*   **Buttons:** Primary buttons are solid Sage Green with White text. Secondary buttons use a charcoal outline with no fill. The transition on hover should be a soft fade to a slightly darker tint of green.
*   **Chips:** Used for plant categories (e.g., "Low Light," "Pet Friendly"). These should use the Terracotta color at 10% opacity for the background with full-opacity Terracotta text.
*   **Inputs:** Clean lines with 1px charcoal borders at 20% opacity. Upon focus, the border transitions to the primary Sage Green.
*   **Cards:** Use a white background against the sand-colored page. Ensure a "float" effect using the ambient shadows defined in Elevation. Product titles in the cards must be EB Garamond.
*   **Data Indicators:** For "Green Tech" specs (Hardiness, Growth Rate), use thin linear progress bars or minimal circular icons rather than bulky gauges.
*   **Botanical Lists:** Use custom iconography for plant care (e.g., a stylized sun or water droplet) using the Primary color.