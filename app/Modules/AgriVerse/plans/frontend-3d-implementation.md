# Kế hoạch triển khai Frontend & Tích hợp 3D/AR — AgriVerse Hub

> **Stack:** Laravel 12 + Inertia.js + Vue 3 (Composition API) + TypeScript + Tailwind CSS v4 + PrimeVue 4 + Three.js + @google/model-viewer
>
> **Trạng thái hiện tại:** Backend Laravel đã hoàn thiện (Models, Controllers, Routes, Migrations). Blade views hiện tại là tạm thời. Cần chuyển sang Inertia + Vue 3.

---

## Nguyên tắc xuyên suốt (Global Rules)

1. **Cái dễ làm trước, quan trọng làm trước** — ưu tiên những tính năng core (xem sản phẩm, giỏ hàng) trước 3D
2. **Tối ưu hiệu năng** — 3D chỉ load khi cần (lazy load), không kéo theo toàn bộ Three.js bundle ngay từ đầu
3. **Giữ nguyên Backend** — không sửa Model/Controller hiện tại, chỉ gọi API/Inertia từ Frontend
4. **Mỗi Phase phải có kết quả kiểm tra được** — chạy được trên browser, không có lỗi console

---

## Giai đoạn 1: Dựng khung giao diện (Frontend Foundation)

### Phase 1.1 — Khởi tạo & Cấu hình Inertia + Vue 3

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Laravel có thể render Vue component thay vì Blade, route Inertia hoạt động |
| **Công nghệ** | `laravel/breeze` (Inertia Vue stack), `@inertiajs/vue3`, `@inertiajs/server`, `ziggy` |
| **Thời gian** | 1–2 ngày |

**Tasks:**
1. Cài Breeze stack Inertia + Vue:
   ```bash
   composer require laravel/breeze --dev
   php artisan breeze:install vue
   npm install
   ```
2. Xoá bỏ package cũ: `inertia-vue`, `vue2` nếu có (vì Breeze sẽ dùng Vue 3)
3. Cấu hình `.env`: `APP_URL`, `VITE_APP_URL`
4. Chạy `npm run dev` và kiểm tra trang Welcome render từ Vue
5. Cấu hình Ziggy (định tuyến từ Vue):
   ```bash
   npm install ziggy-js
   ```
6. Tạo Inertia middleware/layout cơ bản kế thừa Breeze
7. Tích hợp Tailwind v4 (đã có sẵn, chỉ cần sync cấu hình)

**Deliverables:** ✅ `npm run dev` chạy, browser hiển thị Vue component. Route `http://localhost:8000` render từ Inertia.

---

### Phase 1.2 — PrimeVue & UI Foundation

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Có bộ component UI sẵn sàng (button, form, table, dialog, toast) |
| **Công nghệ** | `primevue` 4.x, `@primevue/themes`, `@primevue/forms` |
| **Thời gian** | 1 ngày |

**Tasks:**
1. Cài PrimeVue 4:
   ```bash
   npm install primevue @primevue/themes @primevue/forms
   ```
2. Cấu hình PrimeVue plugin trong `app.js`:
   ```js
   import PrimeVue from 'primevue/config';
   import Aura from '@primevue/themes/aura';
   app.use(PrimeVue, {
     theme: { preset: Aura, options: { darkModeSelector: '.dark-mode' } }
   });
   ```
3. Tạo theme tùy chỉnh: override màu primary thành emerald (#059669) — match thương hiệu AgriVerse
4. Import component cần thiết global (Button, InputText, Toast, Dialog, Card)
5. Tạo `app/Providers/InertiaServiceProvider.php` để share dữ liệu toàn cục (user, cart count, notification)
6. Xây dựng `HandleInertiaRequests.php` middleware share dữ liệu cho mọi request Inertia

**Deliverables:** ✅ Có Button, Toast, Dialog PrimeVue render đúng theme xanh lá. Có thể gọi `primevue/toast` từ bất kỳ component nào.

---

### Phase 1.3 — Master Layout & Navigation

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Giao diện tổng thể: header, footer, sidebar, breadcrumb, layout responsive |
| **Công nghệ** | Vue 3 Composition API, Tailwind, PrimeVue Menubar |
| **Thời gian** | 2–3 ngày |

**Tasks:**
1. Tạo `Layouts/MarketplaceLayout.vue` — layout chính cho khu vực marketplace
2. Tạo `Layouts/AdminLayout.vue` — layout riêng cho admin (sidebar trái)
3. Component `Navbar.vue`:
   - Logo AgriVerse
   - Search bar (gọi API tìm kiếm real-time)
   - Navigation: Sản phẩm, Danh mục, Gian hàng
   - Icon wishlist + cart (có badge số lượng)
   - Dropdown user menu (đăng nhập/đăng xuất)
4. Component `Footer.vue` — 4 cột: Về chúng tôi, Sản phẩm, Hỗ trợ, Kết nối
5. Component `Breadcrumb.vue` — dynamic breadcrumb dựa trên route hiện tại
6. Responsive: mobile navigation (sidebar overlay hoặc bottom nav)
7. Sticky header với `shadow-sm`, transition khi scroll

**Deliverables:** ✅ Load trang MarketplaceLayout thấy header + footer + content area. Resize trình duyệt thấy responsive.

---

### Phase 1.4 — Authentication Flow (Inertia)

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Trang Login, Register, Forgot Password dùng Vue component (thay Blade) |
| **Công nghệ** | Breeze Inertia, PrimeVue Input/Button |
| **Thời gian** | 1–2 ngày |

**Tasks:**
1. Kế thừa Breeze pages có sẵn, custom lại giao diện
2. Tạo trang `Pages/Auth/Login.vue`:
   - Form với PrimeVue InputText + Password + Button
   - Validation inline (required, email format)
   - Loading state khi submit
   - Error message từ server dạng toast
3. Tạo trang `Pages/Auth/Register.vue`
4. Tạo trang `Pages/Auth/ForgotPassword.vue`
5. Tạo trang `Pages/Auth/ResetPassword.vue`
6. Redirect sau login: admin → /admin/agriverse, user → /agriverse (giữ nguyên logic từ LoginController hiện tại)
7. Kiểm tra luồng: đăng nhập → redirect → logout → redirect

**Deliverables:** ✅ Click "Đăng nhập" thấy form Vue đẹp. Đăng nhập thành công → redirect đúng role. Sai mật khẩu → báo lỗi.

---

### Phase 1.5 — Marketplace Pages (Core Shopping)

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Trang chủ, danh sách sản phẩm, chi tiết sản phẩm, gian hàng, danh mục |
| **Công nghệ** | PrimeVue DataView / Card, Inertia pagination, Tailwind grid |
| **Thời gian** | 4–5 ngày |

**Tasks:**

**Trang chủ** (`Pages/Marketplace/Home.vue`):
1. Banner hero với gradient + CTA buttons
2. Danh mục nổi bật (8 icon) — click vào category filter
3. Sản phẩm nổi bật (grid 4 cột) — có giá, discount badge, nút "Thêm vào giỏ"
4. Gian hàng nổi bật (list 3 cột) — redirect đến store detail
5. Thiết kế giống Tiki/Shopee mini: card trắng, shadow nhẹ, hover hiệu ứng

**Trang Sản phẩm** (`Pages/Marketplace/Products/Index.vue`):
1. Grid sản phẩm 4 cột (12 col grid)
2. Sidebar filter: category (radio), price range (input), sort dropdown
3. Pagination Inertia (click trang → không reload)
4. Empty state khi không có kết quả

**Trang Chi tiết Sản phẩm** (`Pages/Marketplace/Products/Show.vue`):
1. Ảnh sản phẩm (slider nếu có multiple images)
2. Thông tin: tên, giá, so sánh giá, discount badge
3. Trạng thái: còn hàng/hết hàng, số lượng đã bán
4. Nút: Thêm vào giỏ hàng (+ số lượng stepper) + Mua ngay
5. Wishlist toggle (trái tim)
6. Thông tin gian hàng (avatar + tên + link)
7. Thông số kỹ thuật (table)
8. Đánh giá (star rating, comment, user avatar)
9. Sản phẩm liên quan (carousel 4 items)
10. **Placeholder cho 3D viewer** (sẽ tích hợp ở Phase 2)

**Trang Gian hàng** (`Pages/Marketplace/Stores/`):
1. Index: grid card gian hàng (avatar + tên + số sản phẩm + mô tả)
2. Show: Header gian hàng + danh sách sản phẩm của gian hàng đó

**Trang Danh mục** (`Pages/Marketplace/Categories/Index.vue`):
1. Grid category card (icon + tên + số lượng + children tags)

**Deliverables:** ✅ Click "Sản phẩm" thấy grid 4 cột. Click vào sản phẩm thấy detail. Filter hoạt động. Pagination không reload trang.

---

### Phase 1.6 — Cart & Checkout

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Giỏ hàng, thanh toán, đặt hàng hoàn chỉnh |
| **Công nghệ** | PrimeVue DataTable, Inertia form, form validation |
| **Thời gian** | 2–3 ngày |

**Tasks:**
1. **Cart Page** (`Pages/Marketplace/Cart/Index.vue`):
   - Danh sách sản phẩm trong giỏ (ảnh + tên + số lượng + giá + tổng)
   - Số lượng adjuster (tăng/giảm)
   - Nút xoá từng item
   - Summary sidebar: tạm tính, phí ship (miễn phí), tổng cộng
   - Button "Thanh toán" → đến checkout
2. **Checkout Page** (`Pages/Marketplace/Checkout/Index.vue`):
   - Form địa chỉ giao hàng
   - Mã giảm giá (coupon)
   - Order summary sidebar
   - Button "Xác nhận đặt hàng" → POST Inertia
3. **Order Success** (`Pages/Marketplace/Checkout/Success.vue`):
   - Thông báo đặt hàng thành công, mã đơn hàng
   - Nút "Xem đơn hàng" → /don-hang/{id}
4. Tích hợp Toast thông báo khi thêm vào giỏ / xoá / đặt hàng thành công

**Deliverables:** ✅ Thêm sản phẩm vào giỏ → thấy badge trên icon. Vào /gio-hang thấy sản phẩm. Điều chỉnh số lượng được. Checkout → redirect đến success page.

---

### Phase 1.7 — User Dashboard & Orders

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | User xem được đơn hàng, wishlist, thông tin cá nhân |
| **Công nghệ** | PrimeVue DataTable, Timeline, TabView |
| **Thời gian** | 2 ngày |

**Tasks:**
1. **Orders List** (`Pages/Marketplace/Orders/Index.vue`):
   - Danh sách đơn hàng (mã, sản phẩm, số lượng, tổng tiền, status badge)
   - Filter theo trạng thái (tab: Tất cả/Chờ xử lý/Đang giao/Đã giao/Đã huỷ)
2. **Order Detail** (`Pages/Marketplace/Orders/Show.vue`):
   - Thông tin đơn hàng + sản phẩm
   - Timeline trạng thái (PrimeVue Timeline)
   - Tổng tiền breakdown
3. **Wishlist** (`Pages/Marketplace/Wishlist/Index.vue`):
   - Grid sản phẩm yêu thích
   - Nút "Thêm vào giỏ" + "Xoá khỏi yêu thích"
4. **Profile** (`Pages/Marketplace/Profile/Edit.vue`):
   - Form cập nhật thông tin (tên, email, số điện thoại, địa chỉ)
   - Đổi mật khẩu

**Deliverables:** ✅ Đặt hàng → vào /don-hang thấy đơn hàng. Click vào thấy chi tiết + timeline. Wishlist hiển thị đúng.

---

### Phase 1.8 — Admin Panel

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Quản lý sản phẩm, đơn hàng, gian hàng, danh mục |
| **Công nghệ** | PrimeVue DataTable, Dialog, Form, TabView, Chart.js |
| **Thời gian** | 4–5 ngày |

**Tasks:**
1. Layout Admin: sidebar trái (Dashboard, Products, Orders, Stores, Categories, Coupons, Reports)
2. **Dashboard**: thống kê (tổng đơn hàng, doanh thu, sản phẩm mới) — PrimeVue Chart
3. **Products CRUD**: DataTable với search/sort/filter, inline status toggle, Dialog form thêm/sửa
4. **Orders Management**: DataTable orders, Dialog chi tiết, dropdown cập nhật trạng thái
5. **Stores Management**: DataTable stores, duyệt/từ chối, xem chi tiết
6. **Categories Management**: tree structure với children
7. **Coupons Management**: CRUD mã giảm giá

**Deliverables:** ✅ /admin/agriverse → thấy dashboard. CRUD sản phẩm từ admin. Duyệt đơn hàng.

---

### Phase 1.9 — Responsive & Polish

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Giao diện đẹp trên mobile, animation mượt, loading state |
| **Công nghệ** | Tailwind responsive, PrimeVue Skeleton, Vue Transition |
| **Thời gian** | 2–3 ngày |

**Tasks:**
1. Test responsive tất cả page trên mobile (320px), tablet (768px), desktop (1200px)
2. Skeleton loading cho product grid (PrimeVue Skeleton)
3. Vue Transition cho page change (slide/fade)
4. Toast notification cho mọi action (cart, order, wishlist)
5. Empty state đẹp cho mọi danh sách (icon + message + CTA)
6. Error page (403, 404, 500) custom
7. Loading spinner khi submit form
8. Kiểm tra performance: Lighthouse, bundle size

**Deliverables:** ✅ Lighthouse > 80. Không có lỗi console. Animation mượt.

---

## Giai đoạn 2: Tích hợp 3D/AR

### Phase 2.1 — Setup Three.js Environment

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Three.js sẵn sàng trong Vue 3, lazy load, không ảnh hưởng performance |
| **Công nghệ** | `three` (npm), `@vueuse/core`, `@tresjs/core` (Vue wrapper cho Three.js) |
| **Thời gian** | 2 ngày |

**Tasks:**
1. Cài Three.js + TresJS (Vue component cho Three.js):
   ```bash
   npm install three @tresjs/core @tresjs/cientos
   ```
2. Tạo `Composables/useThreeScene.js` — setup scene, camera, renderer cơ bản
3. Cấu hình lazy load: chỉ import Three.js khi vào trang có 3D viewer
4. Tạo component `ThreeScene.vue` — container canvas chiếm 100% diện tích cha
5. Tối ưu: `renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))`
6. Test với hình khối đơn giản (cube/sphere) để kiểm tra setup

```vue
<script setup>
// Ví dụ cấu hình lazy load dynamic import
const Renderer = defineAsyncComponent(() => import('./ThreeScene.vue'))
</script>
```

**Deliverables:** ✅ Mở trang có 3D viewer thấy cube/sphere xoay. Không load Three.js khi ở trang khác.

---

### Phase 2.2 — 3D Product Viewer Core

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Viewer xoay 3D sản phẩm: orbit controls, zoom, pan, auto-rotate |
| **Công nghệ** | Three.js + TresJS + OrbitControls + @vueuse/core (useMouse, useScroll) |
| **Thời gian** | 3–4 ngày |

**Tasks:**
1. Xây dựng `Components/Product3DViewer.vue`:
   - **OrbitControls**: chuột kéo xoay, scroll zoom, giữa chuột pan
   - **Auto-rotate**: mặc định xoay chậm, dừng khi user tương tác
   - **Ground plane**: mặt phẳng bóng phản chiếu nhẹ bên dưới model
   - **Background**: gradient nhẹ hoặc environment map dạng studio
   - **Loading skeleton**: hiển thị skeleton trong lúc load model
2. Component props:
   - `modelUrl: string` — URL file .glb/.gltf
   - `autoRotate?: boolean` — mặc định true
   - `backgroundColor?: string` — mặc định #f5f5f4 (stone-50)
3. Lighting setup:
   - Ambient light (yếu, 0.5)
   - Directional light (mạnh, từ góc 45°)
   - Environment map (HDRI studio lighting — file nhẹ)
4. Camera: perspective, fit model vào viewport tự động
5. Controls UI overlay:
   - Nút fullscreen
   - Nút reset camera
   - Nút toggle auto-rotate
   - Indicator "Kéo để xoay 360°"

**Deliverables:** ✅ Mở chi tiết sản phẩm → thấy 3D viewer. Kéo chuột xoay được. Zoom được. Auto-rotate hoạt động.

---

### Phase 2.3 — GLTF/GLB Model Loader

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Load model .glb từ server/local, xử lý lỗi khi model không tồn tại |
| **Công nghệ** | `three/examples/jsm/loaders/GLTFLoader.js`, `three/examples/jsm/loaders/DRACOLoader.js` |
| **Thời gian** | 2 ngày |

**Tasks:**
1. Cấu hình GLTFLoader với DRACO decoder (nén mesh)
2. Tạo composable `useGLTFLoader.js`:
   ```js
   // useGLTFLoader.js
   import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js'
   import { DRACOLoader } from 'three/addons/loaders/DRACOLoader.js'

   export function useGLTFLoader() {
     const loader = new GLTFLoader()
     const dracoLoader = new DRACOLoader()
     dracoLoader.setDecoderPath('/draco/')
     loader.setDRACOLoader(dracoLoader)

     const loadModel = (url) => {
       return new Promise((resolve, reject) => {
         loader.load(url, resolve, undefined, reject)
       })
     }

     return { loadModel }
   }
   ```
3. Xử lý khi model không có:
   - Fallback về letter avatar (giống Blade hiện tại)
   - Thông báo "Mô hình 3D chưa có — Xem ảnh 2D"
4. Download Draco decoder files về `public/draco/` (từ Three.js examples)
5. Progress bar khi load model nặng (> 5MB)

**Deliverables:** ✅ Model .glb load được từ public/storage. Fallback hoạt động. Progress bar hiển thị khi load.

---

### Phase 2.4 — 3D Model Management (Admin)

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Admin upload model 3D, quản lý file, ánh xạ model với sản phẩm |
| **Công nghệ** | PrimeVue FileUpload, Laravel Storage, Model Resource |
| **Thời gian** | 2 ngày |

**Tasks:**
1. Upload form: PrimeVue FileUpload với giới hạn .glb, .gltf, .zip (max 50MB)
2. Laravel endpoint: POST `/admin/agriverse/products/{product}/3d-model`:
   ```php
   // routes/admin.php
   Route::post('products/{product}/3d-model', [ProductController::class, 'upload3dModel'])
      ->name('products.upload-3d-model');

   // ProductController
   public function upload3dModel(Request $request, Product $product) {
     $request->validate(['model' => 'required|file|mimes:glb,gltf,zip|max:51200']);
     $path = $request->file('model')->store('3d-models', 'public');
     $product->update(['model_3d_path' => $path]);
     return back()->with('success', 'Đã upload mô hình 3D.');
   }
   ```
3. Thêm cột `model_3d_path` vào products table (migration mới)
4. Hiển thị preview model 3D trong admin product edit (dùng ThreeScene nhỏ)
5. Delete model cũ khi upload model mới

**Deliverables:** ✅ Admin upload file .glb → lưu vào storage. Product detail page load được model.

---

### Phase 2.5 — AR Quick Look (Mobile AR)

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | User xem sản phẩm trong không gian thực tế qua camera (AR) |
| **Công nghệ** | `<model-viewer>` (Google), WebXR, `<model-viewer>` AR button |
| **Thời gian** | 2–3 ngày |

**Tasks:**
1. Cài `@google/model-viewer`:
   ```bash
   npm install @google/model-viewer
   ```
2. Tạo component `ARViewer.vue`:
   ```vue
   <template>
     <model-viewer
       :src="modelUrl"
       ar
       ar-modes="scene-viewer webxr quick-look"
       camera-controls
       auto-rotate
       class="w-full aspect-square"
       shadow-intensity="1"
       environment-image="neutral"
     />
   </template>
   ```
3. Tích hợp vào Product detail page:
   - Tab: "Xem 3D" ↔ "Xem AR" (chỉ hiện trên mobile)
   - Nút "Xem trong không gian thực" mở AR
4. Fallback: nếu trình duyệt không hỗ trợ AR, ẩn nút AR
5. Kiểm tra: Android Chrome (WebXR), iOS Safari (Quick Look)

```vue
<script setup>
import '@google/model-viewer'
import { onMounted, ref } from 'vue'

const arSupported = ref(false)

onMounted(() => {
  arSupported.value = 'xr' in navigator
})
</script>
```

**Deliverables:** ✅ Trên Android/iOS, click "Xem AR" → mở camera AR với model sản phẩm. Desktop ẩn nút AR.

---

### Phase 2.6 — Performance Optimization

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Web không bị nặng vì 3D, load nhanh, FPS ổn định |
| **Công nghệ** | Dynamic import, Web Worker, LOD, texture compression |
| **Thời gian** | 2 ngày |

**Tasks:**
1. **Lazy load Three.js**: chỉ import khi user mở tab "Xem 3D"
   ```js
   const ThreeViewer = defineAsyncComponent(() =>
     import('@/Components/Product3DViewer.vue')
   )
   ```
2. **Compression**: dùng Draco compression cho GLB files
3. **LOD (Level of Detail)**: giảm polygon khi camera xa
4. **Memoize camera/renderer**: không tạo scene mới mỗi lần mount component (dùng `keep-alive`)
5. **Giới hạn FPS**: requestAnimationFrame với frame skip nếu tab không active
6. **Network**: CDN cho Three.js bundle (unpkg/jsdelivr)
7. **Bundle phân tích**: `npx vite-bundle-analyzer` kiểm tra kích thước sau khi tích hợp

```js
// Tối ưu: dùng useEventListener từ @vueuse/core
import { useEventListener } from '@vueuse/core'

// Chỉ render 3D khi tab active
useEventListener(document, 'visibilitychange', () => {
  if (document.hidden) renderer.setAnimationLoop(null)
  else renderer.setAnimationLoop(animate)
})
```

**Deliverables:** ✅ Load trang product detail không 3D → bundle không chứa Three.js. Mở tab 3D → load Three.js (lazy). FPS > 30 trên thiết bị tầm trung.

---

### Phase 2.7 — 3D Scanning Integration (Store Owner)

| Mục | Chi tiết |
|-----|----------|
| **Mục tiêu** | Chủ gian hàng scan sản phẩm thực tế → tạo model 3D |
| **Công nghệ** | WebXR (immersive-ar), PhotoCatch (iOS), Camera → NeRF API |
| **Thời gian** | 3–4 ngày |

**Tasks:**
1. Tạo component `ModelScanner.vue`:
   - Hướng dẫn user quay video 360° sản phẩm
   - Upload video lên server → gửi đến service tạo 3D (temporary placeholder)
2. Tích hợp nút "Tạo mô hình 3D" trong admin product form
3. Endpoint nhận file + trả về model URL (có thể dùng background job xử lý)
4. Hiển thị trạng thái: "Đang xử lý..." → "Hoàn thành" → preview trong viewer
5. **Tương lai**: có thể tích hợp API từ các dịch vụ như KIRI Engine, Luma AI, hoặc Polycam

**Lưu ý:** Phần này phụ thuộc vào dịch vụ bên thứ 3. Trong Phase 2, chỉ cần tạo giao diện và endpoint, kết quả là mock. Sẽ hoàn thiện ở Phase sau.

**Deliverables:** ✅ Admin upload video, thấy trạng thái "Đang xử lý" → "Hoàn thành". Model xuất hiện trong viewer (có thể dùng model mẫu để test).

---

## Roadmap Tổng thể

```
Phase 1.1 ── Phase 1.2 ── Phase 1.3 ── Phase 1.4
                              │
                              ▼
                   Phase 1.5 ── Phase 1.6 ── Phase 1.7
                              │
                              ▼
                   Phase 1.8 ── Phase 1.9
                              │
                              ▼
                   Phase 2.1 ── Phase 2.2 ── Phase 2.3
                              │
                              ▼
                   Phase 2.4 ── Phase 2.5 ── Phase 2.6
                              │
                              ▼
                         Phase 2.7
```

**Giai đoạn 1:** ~20–25 ngày (1 dev)
**Giai đoạn 2:** ~15–18 ngày (1 dev, có thể song song với GĐ1 phần setup)
**Tổng cộng:** ~35–43 ngày làm việc

---

## Checklist Lưu Ý

- [ ] Backup database trước khi chạy migration mới (`model_3d_path`)
- [ ] Kiểm tra compatibility Three.js + Vue 3 + Vite (đã hỗ trợ tốt)
- [ ] Draco decoder files cần được copy vào `public/` (không có sẵn)
- [ ] model-viewer chỉ hoạt động trên HTTPS (hoặc localhost)
- [ ] AR chỉ hoạt động trên mobile (iOS Safari 12+, Android Chrome 70+)
- [ ] File GLB nên được nén (Draco) trước khi upload — dung lượng lý tưởng < 5MB
- [ ] Test kỹ trên Chrome (desktop + Android) và Safari (iOS)
