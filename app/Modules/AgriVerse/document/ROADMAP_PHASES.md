# Kế Hoạch Phát Triển Sàn TMĐT Nông Nghiệp AgriVerse

## Phase 1 — Fix Schema & Core
- Đồng bộ Contract model vs table
- Đồng bộ Subscription model vs table
- Đồng bộ AiScanningJob model vs table
- Fix route key tất cả model về `id` (dễ dùng)

## Phase 2 — Tables & Models
- Tạo migration: carts, wishlists, categories, reviews, payments, notifications
- Tạo Model tương ứng

## Phase 3 — Cart & Wishlist API
- CartController (CRUD)
- WishlistController (CRUD)
- Requests + Resources

## Phase 4 — Payment
- Payment processing (COD, transfer)
- Transaction history API

## Phase 5 — Reviews & Ratings
- ReviewController
- Rating system cho sản phẩm

## Phase 6 — Categories & Search
- Category tree
- Full-text search improve

## Phase 7 — Order Admin + Dashboards
- Admin order management views
- Seller dashboard (revenue, orders)
- Buyer dashboard (order history)

## Phase 8 — Notifications ✅
- In-app notification system (API CRUD: list, unread count, mark read, mark all read, delete)
- NotificationController + routes in AgriVerse API

## Phase 9 — Discounts/Coupons ✅
- Coupon migration + Model (`code, type, value, min_order_amount, usage_limit, dates`)
- CouponController (list, show, validate endpoint)
- Admin CouponController + views (index/create/edit/destroy)
- Order coupon integration: `coupon_id`, `discount_amount` fields + apply in OrderController::store
- OrderResource updated with `discount_amount`, `total_amount`

## Phase 10 — Commission Engine & Reporting ✅
- Auto-calculate `commission_fee` (5%) on order create (post-discount)
- Admin ReportController: revenue, commission, sellers reports
- Report views: revenue stats (with period filters), commission breakdown, seller leaderboard
- Seller API reports: revenue/commission/stats
- Buyer API stats: total spent/orders/pending
- Admin dashboard updated: revenue, commission, reviews, pending orders, recent orders list
- Sidebar: new links for Coupons, Reports
