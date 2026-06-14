# AgriVerse Hub: Frontend & 3D Experience

## 1. Technology Stack
- **Core 3D Engine:** Three.js or Babylon.js. Mandatory for displaying 3D models, 360-degree rotation, zoom, and Exploded View.
- **WebXR API:** Enables Augmented Reality (AR) directly in mobile browsers without app installation. Supports 1:1 scale "fitting" in real space.
- **Frontend Framework:** React.js, Vue.js, or Next.js (Next.js is recommended in the roadmap).
- **Styling:** Responsive Design (Mobile & Desktop).

## 2. Key Features
- **FR-01 (3D Interaction):** 360-degree rotation, zoom in/out.
- **FR-02 (WebAR):** 1:1 scale AR projection via mobile camera.
- **FR-03 (Exploded View & Animation):** Disassemble machinery components and view operational animations.
- **FR-04 (Fallback Mechanism):** Automatically display high-quality 2D images or 360-degree videos if the user's device does not support WebGL/WebAR, ensuring no "white screen" errors.
- **Digital Showroom:** Dark mode/Studio grey background to highlight mechanical details.

## 3. UI/UX Design Philosophy
- **Agri-Tech Minimalism:** Minimalist interface using neutral tones (white, light grey) with signature "Agricultural Green" accents.
- **Product-Centric:** Focus on the 3D viewer (occupying ~60% of the screen on product pages).
- **Mobile-First for Aesthetics:** Intuitive gestures (swipe to rotate, pinch to zoom) for bonsai/plant models.
- **Desktop-Optimized for Machinery:** Complex controls for technical exploration.

## 4. Performance Goals
- **Load Time:** Page and 3D models must load in under 3 seconds (even on 4G).
- **Compression:** Use **Draco compression** for 3D assets to minimize file size.
- **Techniques:** Implement Lazy Loading and Level of Detail (LOD) to prevent performance drops.

## 5. Prototype Workflow
1. **Explore:** User browses product list -> Clicks a Bonsai.
2. **3D Interaction:** Web page loads model < 3s -> User rotates/inspects.
3. **AR Experience:** User clicks "Try AR" on mobile -> Camera opens -> Plant appears in their garden.
4. **Decision:** View technical specs (Digital Passport) -> Click "Connect to Seller".
