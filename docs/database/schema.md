# Database Schema (MySQL)

## Tables

### `users`
- `id` — BIGINT PK, auto-increment
- `name` — VARCHAR(255)
- `email` — VARCHAR(255) UNIQUE
- `password` — VARCHAR(255), hashed
- `phone` — VARCHAR(20) nullable
- `avatar` — TEXT nullable
- `gender` — TINYINT default 0
- `group` — TINYINT default 0
- `active` — TINYINT default 1
- `is_active` — TINYINT default 1
- `birthday` — DATE nullable
- `email_verified_at` — TIMESTAMP nullable
- `remember_token` — VARCHAR(100) nullable
- `timestamps`

### `categories`
- `id` — BIGINT PK
- `name` — VARCHAR(255)
- `slug` — VARCHAR(255) UNIQUE
- `description` — TEXT nullable
- `image` — TEXT nullable
- `parent_id` — BIGINT nullable FK → categories.id
- `order` — INTEGER default 0
- `active` — TINYINT default 1
- `timestamps`

### `products`
- `id` — BIGINT PK
- `name` — VARCHAR(255)
- `slug` — VARCHAR(255) UNIQUE
- `category_id` — BIGINT FK → categories.id
- `description` — TEXT nullable
- `price` — DECIMAL(15,2)
- `unit` — VARCHAR(50)
- `quantity` — INTEGER default 0
- `images` — JSON nullable
- `active` — TINYINT default 1
- `timestamps`

### `orders`
- `id` — BIGINT PK
- `user_id` — BIGINT FK → users.id
- `code` — VARCHAR(50) UNIQUE
- `total` — DECIMAL(15,2)
- `status` — ENUM(pending,confirmed,shipping,delivered,completed,cancelled)
- `payment_method` — VARCHAR(50)
- `payment_status` — VARCHAR(50)
- `note` — TEXT nullable
- `address` — TEXT
- `timestamps`

### `order_items`
- `id` — BIGINT PK
- `order_id` — BIGINT FK → orders.id
- `product_id` — BIGINT FK → products.id
- `price` — DECIMAL(15,2)
- `quantity` — INTEGER
- `total` — DECIMAL(15,2)
- `timestamps`

### `cart_items`
- `id` — BIGINT PK
- `user_id` — BIGINT FK → users.id
- `product_id` — BIGINT FK → products.id
- `quantity` — INTEGER
- `timestamps`

### `product_assets`
- `id` — BIGINT PK
- `product_id` — BIGINT FK → products.id
- `type` — VARCHAR(50)
- `url` — TEXT
- `position` — INTEGER default 0
- `active` — TINYINT default 1
- `timestamps`

### `forums`
- `id` — BIGINT PK
- `title` — VARCHAR(255)
- `slug` — VARCHAR(255)
- `content` — TEXT
- `user_id` — BIGINT FK → users.id
- `category` — VARCHAR(100)
- `tags` — JSON nullable
- `views` — INTEGER default 0
- `status` — ENUM(published,draft,archived)
- `timestamps`

### `forum_comments`
- `id` — BIGINT PK
- `forum_id` — BIGINT FK → forums.id
- `user_id` — BIGINT FK → users.id
- `content` — TEXT
- `timestamps`

### `stores`
- `id` — BIGINT PK
- `name` — VARCHAR(255)
- `slug` — VARCHAR(255) UNIQUE
- `description` — TEXT nullable
- `user_id` — BIGINT FK → users.id
- `phone` — VARCHAR(20) nullable
- `address` — TEXT nullable
- `lat` — DOUBLE nullable
- `lng` — DOUBLE nullable
- `cover_image` — TEXT nullable
- `active` — TINYINT default 1
- `timestamps`

### `personal_access_tokens`
- Passport OAuth tokens table
- `id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`

### `oauth_access_tokens`
- Passport access tokens (15min TTL)
- `id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`

### `oauth_refresh_tokens`
- Passport refresh tokens (30d TTL)
- `id`, `access_token_id`, `revoked`, `expires_at`

### `oauth_clients` / `oauth_personal_access_clients`
- Passport clients configuration

### `permissions` / `roles` / `model_has_permissions` / `model_has_roles` / `role_has_permissions`
- Spatie Permission package tables

## Key Indexes
- `products.category_id`, `products.slug`
- `orders.user_id`, `orders.code`, `orders.status`
- `order_items.order_id`, `order_items.product_id`
- `cart_items.user_id`, `cart_items.product_id`
- `forums.slug`, `forums.status`
- `stores.slug`

## ER Diagram
```
users 1──* orders
users 1──* cart_items
users 1──* forum_comments
orders 1──* order_items
categories 1──* products
products 1──* order_items
products 1──* cart_items
products 1──* product_assets
stores 1──* users
```
