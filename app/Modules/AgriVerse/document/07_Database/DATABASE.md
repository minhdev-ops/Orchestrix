# AgriVerse Hub: Database Schema & Data Model

## 1. Database Standards
- **Engine:** MySQL 8.0+ or PostgreSQL 14+.
- **Integrity:** Strict Foreign Key constraints and indexing on search-heavy fields (e.g., product categories, store IDs).

## 2. Core Tables
### 2.1. User & Access Management (RBAC)
- **users:** `id`, `email`, `password`, `profile_info` (JSON), `created_at`, `updated_at`.
- **roles:** `id`, `name` (Admin, Seller_Owner, Seller_Staff, Buyer).
- **permissions:** `id`, `name` (View 3D, Manage Inventory, Configure Commissions, etc.).
- **role_has_permissions:** `role_id`, `permission_id`.

### 2.2. Store & Subscription
- **stores:** `id`, `owner_id` (FK: users), `name`, `description`, `logo`, `status` (Active, Suspended).
- **subscription_plans:** `id`, `name` (Basic, Pro, Advanced), `limit_3d_models`, `price_per_month`.
- **store_subscriptions:** `id`, `store_id`, `plan_id`, `start_date`, `end_date`, `payment_status`.

### 2.3. Products & Digital Passport
- **products:** `id`, `store_id`, `category` (Bonsai/Machinery), `name`, `price`, `status`, `technical_specs` (JSON).
- **digital_passport_logs:** `id`, `product_id`, `event_type` (Watering, Maintenance, Part replacement), `event_date`, `description`.

### 2.4. 3D Assets & Optimization
- **asset_3d:** `id`, `product_id`, `optimized_file_path`, `raw_file_path`, `asset_type` (360_View, WebAR_Model, Exploded_View, Animation_Data), `optimization_status` (Pending, Processing, Completed, Failed).

### 2.5. Transactions & AI Jobs
- **orders:** `id`, `buyer_id`, `seller_id`, `product_id`, `total_amount`, `commission_fee`, `status`.
- **contracts:** `id`, `order_id` (FK: orders), `content_hash`, `contract_pdf_path`, `buyer_signed_at`, `seller_signed_at`, `status` (Draft, Signed, Verified).
- **ai_scanning_jobs:** `id`, `store_id`, `source_video_url`, `status`, `result_asset_id` (FK: asset_3d).

## 3. Relationships
- **One Store** has many **Products**.
- **One Product** has many **Asset_3D** (different viewing modes).
- **One Order** links **Buyer**, **Seller**, and **Product**.
