# AgriVerse API Documentation

## Overview

AgriVerse REST API provides endpoints for managing products, orders, users, and more. All endpoints require authentication via Laravel Passport OAuth2 tokens unless noted otherwise.

## Base URL

```
https://api.agriverse.vn/api/v1
```

## Authentication

All authenticated requests must include the `Authorization` header:

```
Authorization: Bearer {your_access_token}
```

## Response Format

All responses follow this format:

```json
{
    "success": true,
    "message": "Success message",
    "data": {},
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

## Error Responses

```json
{
    "success": false,
    "message": "Error message",
    "errors": {}
}
```

---

## Endpoints

### Products

#### List Products
```
GET /products
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| search | string | Search by name/description |
| category | string | Filter by category slug |
| min_price | number | Minimum price |
| max_price | number | Maximum price |
| in_stock | boolean | Only in-stock products |
| sort | string | Sort by: price_asc, price_desc, newest, popular |
| page | int | Page number |
| per_page | int | Items per page (max 50) |

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Cây bonsai mini",
            "price": 250000,
            "image": "https://...",
            "categories": [...],
            "store": {...}
        }
    ]
}
```

#### Get Product
```
GET /products/{id}
```

#### Create Product
```
POST /products
```

**Body:**
```json
{
    "name": "Cây bonsai mini",
    "description": "Mô tả sản phẩm",
    "price": 250000,
    "stock": 10,
    "category_ids": [1, 2]
}
```

#### Update Product
```
PUT /products/{id}
```

#### Delete Product
```
DELETE /products/{id}
```

---

### Orders

#### List Orders
```
GET /orders
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| status | string | Filter by status |
| page | int | Page number |

#### Get Order
```
GET /orders/{id}
```

#### Create Order
```
POST /orders
```

**Body:**
```json
{
    "product_id": 1,
    "quantity": 2,
    "shipping_address": "123 Đường ABC, Hà Nội",
    "notes": "Giao hàng giờ hành chính"
}
```

#### Cancel Order
```
POST /orders/{id}/cancel
```

#### Confirm Order (Seller)
```
POST /orders/{id}/confirm
```

#### Ship Order (Seller)
```
POST /orders/{id}/deliver
```

#### Complete Order (Buyer)
```
POST /orders/{id}/complete
```

---

### Cart

#### Get Cart
```
GET /cart
```

#### Add to Cart
```
POST /cart
```

**Body:**
```json
{
    "product_id": 1,
    "quantity": 2
}
```

#### Update Cart Item
```
PUT /cart/{id}
```

#### Remove from Cart
```
DELETE /cart/{id}
```

#### Clear Cart
```
DELETE /cart
```

---

### Wishlist

#### Get Wishlist
```
GET /wishlist
```

#### Add to Wishlist
```
POST /wishlist
```

#### Remove from Wishlist
```
DELETE /wishlist/{id}
```

---

### Categories

#### List Categories
```
GET /categories
```

#### Get Category
```
GET /categories/{id}
```

#### Get Category Products
```
GET /categories/{id}/products
```

---

### Reviews

#### Get Product Reviews
```
GET /products/{id}/reviews
```

#### Add Review
```
POST /products/{id}/reviews
```

**Body:**
```json
{
    "rating": 5,
    "comment": "Sản phẩm rất tốt!"
}
```

---

### User Profile

#### Update Profile
```
POST /api/user/update
```

**Body:**
```json
{
    "name": "Nguyễn Văn A",
    "phone": "0912345678",
    "gender": "male"
}
```

#### Change Password
```
POST /api/change-pass
```

**Body:**
```json
{
    "oldpass": "current_password",
    "newpass": "new_password",
    "repass": "new_password"
}
```

---

### Notifications

#### Get Notifications
```
GET /notifications
```

#### Get Unread Count
```
GET /notifications/unread-count
```

#### Mark as Read
```
PUT /notifications/{id}/read
```

#### Mark All as Read
```
PUT /notifications/read-all
```

---

### Chat

#### Get Conversations
```
GET /chat/conversations
```

#### Start Conversation
```
POST /chat/conversations
```

**Body:**
```json
{
    "user_id": 2,
    "product_id": 1
}
```

#### Get Messages
```
GET /chat/{conversation_id}/messages
```

#### Send Message
```
POST /chat/{conversation_id}/messages
```

**Body:**
```json
{
    "message": "Xin chào!"
}
```

---

### Coupons

#### List Coupons
```
GET /coupons
```

#### Get Coupon
```
GET /coupons/{id}
```

#### Validate Coupon
```
POST /coupons/validate
```

**Body:**
```json
{
    "code": "SALE10",
    "order_total": 500000
}
```

---

### Plant Doctor (AI)

#### Diagnose Plant
```
POST /plant-doctor/diagnose
```

**Body (multipart/form-data):**
```
image: [image file]
```

#### Get Diagnosis History
```
GET /plant-doctor/history
```

#### Get Diagnosis Details
```
GET /plant-doctor/history/{id}
```

---

## Rate Limiting

API requests are limited to **60 requests per minute** per user. Rate limit headers:

- `X-RateLimit-Limit`: Maximum requests allowed
- `X-RateLimit-Remaining`: Remaining requests
- `X-RateLimit-Reset`: Time when limit resets (Unix timestamp)

When rate limited, response will be:
```json
{
    "message": "Quá nhiều yêu cầu. Vui lòng thử lại sau.",
    "retry_after": 60
}
```
Status: `429 Too Many Requests`

---

## Payment Methods

### Available Methods
| Method | ID | Description |
|--------|-----|-------------|
| COD | cod | Thanh toán khi nhận hàng |
| Banking | banking | Chuyển khoản ngân hàng |
| VNPay | vnpay | ATM, Visa, MasterCard |
| MoMo | momo | Ví điện tử MoMo |

### Create Payment
```
POST /orders/{id}/payment
```

**Body:**
```json
{
    "payment_method": "vnpay"
}
```

**Response:**
```json
{
    "payment_url": "https://sandbox.vnpayment.vn/..."
}
```

---

## Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Invalid/missing token |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation error |
| 429 | Too Many Requests - Rate limited |
| 500 | Server Error - Internal error |

---

*Last updated: 2026-06-15*
