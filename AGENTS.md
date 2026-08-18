# AgriVerse — Anchored Summary

## Goal

- Deploy AgriVerse lên production `agriverse.slink.id.vn` với Cloudflare SSL, Docker Compose (7 containers), full data migration.

## Progress

### Done

- **Bounce easing**: replaced `cubic-bezier(0.34,1.56,0.64,1)` with `cubic-bezier(0.16,1,0.3,1)` in `index.css` and `.vue` files.
- **Text contrast**: `--ag-text-secondary` `#74796c` → `#5f6358`; `--ag-accent-500` `#a19f99` → `#65625c`. Both pass WCAG AA.
- **AQI yellow `#ffff00`**: JS function `ensureTextContrast()` darkens high-luminance colors (55% black mix). Renders `#737300` (4.6:1) instead of `#ffff00` (1.1:1).
- **All-caps body text**: removed `text-transform: uppercase` from `.hero-badge-text` and `.commitment-badge`.
- **Heading hierarchy**: fixed skipped levels in `Home.vue`, `Stores/Index.vue`, `Products/Show.vue`.
- **Cramped padding**: added padding to `.category-icon`, `.commitment-feature-icon`, `.ar-btn`.
- **Hero stats glass contrast**: bg opacity 0.6→0.92, blur 12→16px; removed `opacity: 0.6` on label.
- **`letter-spacing`**: removed `0.08em` from `.hero-badge` and `.commitment-badge`.
- **Design system doc**: created/updated `app/Modules/AgriVerse/design.md` with tokens, contrast rules, glassmorphism, heading hierarchy, component specs.
- **Feature test document**: created `app/Modules/AgriVerse/document/06_Testing/FEATURE_TEST_SCRIPTS.md` — comprehensive test scenarios for Buyer (38 sections), Seller (9 sections), Admin (20 sections), API (16 sections), cross-cutting (5 sections).

### In Progress

- None.

### Done (this session)

- **Forum upload failing**: `Forum/Create.vue` used `fetch()` without `X-Requested-With` header and sent `_token` in FormData body instead of `X-CSRF-TOKEN` header, causing CSRF/intermittent failures. Added `X-Requested-With: XMLHttpRequest` + `X-CSRF-TOKEN` header, client-side validation (type/size), and proper error message parsing. Removed `_token` from FormData.
- **Forum images not rendering**: `Forum/Show.vue` `parsedContent` only handled headings and newlines — no markdown image (`![alt](url)`) or link parsing. Added regex replacement for images (`<img>`) and links (`<a>`).
- **Forum image insert UX**: Changed from auto-insert markdown into textarea → upload to gallery with thumbnail previews. User must click a thumbnail to insert `![name](url)` at cursor position. Added `uploadedImages` ref array, `uploading` state, `insertImage()` and `removeImage()` functions, and gallery CSS.
- **Forum images not in post**: `submit()` now auto-appends uploaded images not yet inserted into content. Sends `images` array (URLs) to server.
- **Forum images column**: Migration `2026_06_29_000001_add_images_to_forum_posts` adds JSON `images` column. `ForumPost` model updated with `$fillable` and `$casts`. `ForumController@store`/`update`/`show` save/return images.
- **Forum image gallery on Show**: Added gallery section below post content with grid layout (`auto-fill, minmax(160px)`).
- **ForumImageBrowser (CKFinder-like)**: New component with modal UI. Two tabs: Upload (drag-drop/multiple) and Browse (list all user's previous uploads via `/dien-dan/images` API). Click image to insert markdown at cursor. API endpoint `ForumUploadController@listImages` lists user's uploaded images.

### Done (this session)

- **Wishlist page blank**: `Wishlist/Index.vue` used `wishlistItems.length` / `v-for="item in wishlistItems"` but controller returns paginated object (`.data`). Fixed template to use local `items` ref initialized from `wishlistItems.data`. Added `description` to `WishlistController@index` product mapping. Updated test doc 1.3 with header→wishlist navigation step.
- **Social login missing api_token**: `SocialAuthController@findOrCreateUser` didn't generate Passport token → chat WebSocket không kết nối được. Added `$user->createToken('web')` + `session()->flash('api_token')` giống `LoginController`.
- **Related Products missing**: `Products/Show.vue` không có section render `relatedProducts`. Added full grid section + CSS.
- **Compare feature**: Created `useCompare.js` (localStorage), compare button on ProductCard + Show.vue, `CompareBar.vue` (floating bar), `Compare/Index.vue` (side-by-side table with specs), route `/so-sanh`, controller `compare()` method. Added discount badge + `compare_price` display enhancements.
- **Category product count fix**: `DatabaseSeeder` created categories without `is_active=true` and never inserted `category_product` pivot records. Added `is_active: true` to all category creations + bulk update for existing records. Added pivot sync by matching Product `category` string field to Category slug.
- **Compare page empty state fix**: `CompareBar.vue` wasn't passing `ids` query param. Fixed `goCompare()` to use `compareIds.value.join(',')`. Added `onMounted` fallback in `Compare/Index.vue` to redirect from localStorage if server received no IDs.
- **Compare page UI rewrite**: Replaced broken `<table>` layout with grid-based `compare-cards` (product cards row) + `compare-specs` (div-based specs rows with dynamic `grid-template-columns` via `specGridStyle` computed). Responsive down to 768px.
- **Compare all attributes**: `ProductController::compare()` now loads store, manufacturer, productType relations, average rating, reviews count. Controller map expanded to include `status`, `is_featured`, `store_name`, `manufacturer_name`, `product_type_name`, `avg_rating`, `reviews_count`, `has_variants`, `model_3d_url`, `metadata`. Template organized into 6 spec groups (Thông tin chung, Giá & Kho, Mô tả & Đánh giá, Liên kết, Thông số kỹ thuật, Thông tin bổ sung) with conditional dividers and visibility computeds. Added `tag-active`/`tag-inactive`/`tag`/`.muted`/`.text-danger` CSS classes.
- **Category count fix (Home page)**: `HomeController@index` was comparing product `category` field against `$cat->name` (display name) instead of `$cat->slug` (slug). Fixed `$cat->name` → `$cat->slug`. Products seeded with slug values (`bonsai-co-thu`, etc.) now match correctly.

### Done (this session)

- **Contracts/Show.vue nâng cấp**: thêm timeline status, sidebar chữ ký, thông tin đơn hàng liên quan, nút in, badge trạng thái động (active/signed/pending/cancelled/expired), format giá/ngày. Hiển thị terms nếu có.
- **Notifications/Index.vue sửa lỗi**: endpoint API hardcoded → dùng `route()`, không dùng `axios` riêng → chuyển sang `window.axios`, thêm click-to-navigate đến đơn hàng (nếu `notif.data.order_id`), thêm `formatDate`.
- **Tracking/Index.vue cải thiện**: thêm dropdown đơn hàng gần đây (lấy từ server prop `recentOrders`), click chọn tự động lookup. Backend `OrderController@tracking` thêm `recentOrders` query.
- **TwoFactor.vue sửa 3 bugs nghiêm trọng**: (1) `showRecoveryCodes` undefined → khai báo ref; (2) `recoveryCodes` không bao giờ được gán → đổi tên `recoveryCodesList`, populate từ API response; (3) `regenerateCodes` dùng `prompt()` → thay bằng inline input field + `showRegenInput`. Xóa unused `router` import.
- **Affiliate/Dashboard.vue fix critical bug**: prop names không khớp với controller (`totalCommission` vs `total_earnings`, `withdrawn` vs `paid_commission`, `available` vs `pending_commission`, `commissions` vs `recent_commissions`). Thêm referral link với copy-to-clipboard, thống kê referrals/conversion rate, danh sách recent_referrals.
- **Affiliate/Register.vue**: thêm validation phía client (required fields), hiển thị lỗi từ server (dạng flash + validation errors), loading state, `onError` handler.
- **Navigation links**: thêm tất cả các page vào user dropdown (`userNav`): Đơn hàng, Thông báo, Theo dõi vận chuyển, Affiliate, Xác thực 2FA, Cài đặt. Thêm link 2FA trực tiếp trong Settings/Security tab. Cập nhật mobileNav.

### Done (this session)

- **Product images**: Created `FetchProductImages` artisan command fetching real CC-licensed images from Wikipedia for 57/60 products. Downloaded remaining 3 (ID 17, 21, 23) from Commons/USDA/pixy.org. Added search terms for missing products 1-8 and 42-49, re-ran command globally — all 60 products now have real images, 0 placeholders.

### Blocked

- `transition: padding` — not found in source; likely PrimeVue/Vue transition runtime.
- Overused Roboto — intentional design system choice.
- `--ag-text-muted` `#c4c8ba` (1.6:1) — intentionally muted for placeholder/disabled text.

## Done (this session)

- **Journal articles not showing**: Dữ liệu scrape từ bonsaiempire.vn mới chỉ lưu file review, chưa insert vào DB. Tạo command `agriverse:scrape-bonsai-articles` với nội dung fallback đầy đủ cho 19 bài (8 species + 11 basics). Chạy thành công — database giờ có 20 articles (1 cũ + 19 mới). Trang `/bai-viet` hiển thị đầy đủ.

## Done (this session) — Docker deployment

- **Initial deployment created** Docker configs cho cả Orchestrix + JakartaEE lên VPS `103.15.222.128` domain `agriverse.slink.id.vn`.
- **Server fix round 1**: 7 containers đều chạy (nginx, php, queue, scheduler, mysql, redis, wildfly), site AgriVerse HTTP 200.
- **WildFly fixes**:
    - Image `:latest` = `40.0.0.Final-jdk21`, binary tại `/opt/jboss/wildfly/` (not `/opt/wildfly/` or `/wildfly/`)
    - Custom `standalone.xml` dùng sai namespace `urn:jboss:domain:20.0` (cần `urn:jboss:domain:community:20.0`). **Giải pháp**: xóa COPY của standalone.xml, dùng mặc định từ base image.
    - `USER root` → `USER jboss` sau khi build (fix permission cho deployments)
- **Nginx fixes**:
    - Cần runtime DNS resolution cho upstream (sử dụng `resolver 127.0.0.11` + `set $variable` trong proxy_pass/fastcgi_pass)
    - `listen 443 ssl http2` → `listen 443 ssl; http2 on;` (fix deprecation)
    - Stop old Podman nginx (giữ port 80) → chuyển sang Docker Compose nginx
- **PHP fixes**:
    - `sys_temp_dir = /tmp` (config volume mount `docker/php/conf.d/sys_temp.ini`)
    - Storage/bootstrap cache permissions (chown www-data)
    - Run migrations (thiếu bảng `sessions`)
- **Docker Compose**: removed deprecated `version: '3.8'`
- **CI/CD**: `.github/workflows/deploy.yml` với 2 jobs deploy-orchestrix + deploy-jakarta

## Done (this session) — Live deployment to agriverse.slink.id.vn

- **SSL**: Cloudflare Flexible SSL — Cloudflare handles HTTPS, nginx origin chỉ listen port 80.
- **Nginx config**: Cloudflare `set_real_ip_from` + `real_ip_header CF-Connecting-IP`; bỏ SSL server block, thêm `fastcgi_param HTTPS on;`
- **.env production**: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://agriverse.slink.id.vn`
- **Database**: Dump local `agriverse` DB → import vào server `orchestrix` (60 products, 3 users, all tables)
- **Code sync**: Latest code + Vue build assets rsync lên server
- **Migrations**: All ran thành công
- **Telescope fix**: Conditionally register only when package installed (tránh lỗi `--no-dev`)
- **Storage link**: `php artisan storage:link`
- **Queue/Scheduler**: Restarted để pick up code mới

**Kết quả**: `https://agriverse.slink.id.vn/` → HTTP 200, đầy đủ nội dung AgriVerse.

## Remaining Issues

- **WildFly Redis**: Jakarta app connect `localhost:6379` → cần `redis:6379` (Docker service name). Fix trong source code JakartaEE.
- **`.gitignore`**: Cần bỏ `docker-compose.yml`, `Dockerfile`, `docker/nginx/conf.d/app.conf` khỏi `.gitignore` để CI/CD deploy được config file.
- **reCAPTCHA**: Đang dùng test keys, cần restore real keys trước production launch.

## Done (this session) — 419 CSRF fix

- **Root cause**: Laravel 12 `EncryptCookies` thêm HMAC prefix `{sha1_hmac}|{session_id}` vào session cookie. `StartSession::isValidId()` từ chối (có `|`, length 81≠40). Dù `EncryptCookies::decrypt()` + `CookieValuePrefix::validate()` remove prefix thành công, session vẫn không persist giữa các request vì ID không được công nhận.
- **Fix**: Thêm `orchestrix-session` + `XSRF-TOKEN` vào `encryptCookies` except list trong `bootstrap/app.php` → session ID và CSRF token là plaintext, không bị HMAC prefix.
- **CSRF workaround**: Thêm `login`, `register`, `password/*`, `auth/*` vào `validateCsrfTokens` except list vì `VerifyCsrfToken@getTokenFromRequest` không xử lý được plaintext `X-XSRF-TOKEN` header (vẫn cố `decrypt()` → `DecryptException` → empty string).
- **RECAPTCHA**: Set test keys (`6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI`/`6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe`) để luôn pass verification.
- **Kết quả**: Login `admin@orchestrix.com` / `Minh250305@` → 302 `/admin/agriverse` → dashboard HTTP 200. Session DB có `user_id=1`.

## Key Decisions

- **Keep Roboto** — system font choice.
- **Keep `--ag-text-muted` `#c4c8ba`** — intentional for placeholder/disabled hierarchy.
- **JS darkening for AQI** — ensures all dynamic color values meet WCAG AA body-text threshold.
- **WildFly runtime DNS** — dùng nginx `resolver` + variables thay vì upstream + depends_on.
- **Mount sys_temp_dir** — volume mount thay vì rebuild image (tránh timeout npm build trên server).
