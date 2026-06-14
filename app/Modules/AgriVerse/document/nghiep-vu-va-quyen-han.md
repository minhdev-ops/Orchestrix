# Nghiệp Vụ & Quyền Hạn Theo Role

## Demo Users

| Email | Password | Role |
|---|---|---|
| admin@orchestrix.com | 12345678 | Admin |
| seller@orchestrix.com | 12345678 | Seller |
| employee@orchestrix.com | 12345678 | Employee |
| buyer@orchestrix.com | 12345678 | Buyer |

---

## 1. Admin — Quản Trị Viên

Toàn quyền trên tất cả module, không bị giới hạn scope dữ liệu.

| Module | Quyền |
|---|---|
| Sản phẩm | Xem tất cả, tạo, sửa, xoá, publish/unpublish bất kỳ sản phẩm nào |
| 3D Assets | Upload, xem, sửa, xoá, compress bất kỳ asset nào |
| Orders | Xem tất cả đơn hàng, tạo đơn, huỷ đơn bất kỳ |
| Contracts | Xem tất cả hợp đồng, tạo, ký, tải PDF |
| Subscriptions | Xem tất cả, tạo, huỷ |
| Stores | Xem tất cả cửa hàng |
| Subscription Plans | Xem tất cả gói |
| Digital Passport | Xem passport, xem/tạo log |
| Users | Quản lý người dùng (web) |
| Settings | Xem và chỉnh sửa cấu hình |
| Report | Xem báo cáo |
| Dashboard | Xem dashboard |

---

## 2. Seller — Người Bán

Quản lý sản phẩm và đơn hàng của riêng mình.

### Products
- `GET /api/products` — Chỉ xem sản phẩm của chính mình
- `GET /api/products/{id}` — Chỉ xem sản phẩm của mình (kể cả draft), nếu không phải của mình → 404
- `POST /api/products` — Tạo sản phẩm (gán user_id = chính mình)
- `PUT /api/products/{id}` — Sửa sản phẩm của mình, nếu không phải của mình → 403
- `DELETE /api/products/{id}` — Xoá sản phẩm của mình, nếu không phải của mình → 403
- `GET /api/products/{id}/assets` — Xem assets của sản phẩm

### 3D Assets
- `GET /api/assets` — Chỉ xem assets của chính mình
- `GET /api/assets/{id}` — Xem asset bất kỳ (không check ownership)
- `POST /api/assets` — Upload asset (gán user_id = chính mình)
- `PUT /api/assets/{id}` — Sửa asset của mình, nếu không phải → 403
- `DELETE /api/assets/{id}` — Xoá asset của mình, nếu không phải → 403
- `POST /api/assets/{id}/compress` — Nén asset của mình, nếu không phải → 403

### Orders
- `GET /api/orders` — Chỉ xem đơn hàng với tư cách seller (seller_id = mình)
- `GET /api/orders/{id}` — Chỉ xem nếu là seller của đơn đó, nếu không → 403
- `POST /api/orders` — Tạo đơn hàng (buyer_id = mình)
- `POST /api/orders/{id}/cancel` — Huỷ đơn (chỉ khi status = pending/confirmed)

### Contracts
- `GET /api/contracts` — Chỉ xem hợp đồng liên quan đến đơn hàng của mình (với tư cách seller)
- `GET /api/contracts/{id}` — Xem chi tiết
- `POST /api/contracts` — Tạo hợp đồng mới
- `POST /api/contracts/{id}/sign` — Ký hợp đồng với tư cách seller (gắn signed_by_seller = true)
- `GET /api/contracts/{id}/pdf` — Tải PDF hợp đồng

### Subscriptions
- `GET /api/subscriptions` — Xem danh sách
- `GET /api/subscriptions/{id}` — Xem chi tiết

### Stores
- `GET /api/stores` — Chỉ xem cửa hàng của mình
- `GET /api/stores/{id}` — Xem chi tiết cửa hàng

### Subscription Plans
- `GET /api/subscription-plans` — Xem danh sách gói
- `GET /api/subscription-plans/{id}` — Xem chi tiết gói

### Digital Passport
- `GET /api/products/{id}/digital-passport` — Xem passport của sản phẩm
- `GET /api/digital-passport-logs` — Xem lịch sử log
- `GET /api/digital-passport-logs/{id}` — Xem chi tiết log
- `POST /api/digital-passport-logs` — Tạo log mới

---

## 3. Employee — Nhân Viên

Hỗ trợ seller quản lý sản phẩm nhưng không được xoá hay publish.

### Products
- `GET /api/products` — Chỉ xem sản phẩm thuộc cửa hàng được gán
- `GET /api/products/{id}` — Xem chi tiết
- `POST /api/products` — Tạo sản phẩm
- `PUT /api/products/{id}` — Sửa sản phẩm

### 3D Assets
- `GET /api/assets` — Xem tất cả assets
- `GET /api/assets/{id}` — Xem chi tiết
- `POST /api/assets` — Upload asset
- `PUT /api/assets/{id}` — Sửa asset

**Không có quyền:** Xoá product, publish product, xoá asset, compress asset

### Views Only
- `GET /api/subscriptions` — Xem subscription
- `GET /api/stores` — Xem store
- `GET /api/subscription-plans` — Xem plan

**Không có quyền:** Orders, Contracts, Digital Passport

---

## 4. Buyer — Người Mua

Chỉ xem được sản phẩm đã publish, tạo và quản lý đơn hàng của mình.

### Products
- `GET /api/products` — Chỉ xem sản phẩm có status = published
- `GET /api/products/{id}` — Chỉ xem nếu sản phẩm đã publish, nếu không → 404
- `GET /api/products/{id}/assets` — Xem assets

### Orders
- `GET /api/orders` — Chỉ xem đơn hàng với tư cách buyer (buyer_id = mình)
- `GET /api/orders/{id}` — Chỉ xem nếu là buyer của đơn đó, nếu không → 403
- `POST /api/orders` — Tạo đơn hàng (buyer_id = mình)
- `POST /api/orders/{id}/cancel` — Huỷ đơn của mình

### Contracts
- `GET /api/contracts` — Chỉ xem hợp đồng liên quan đến đơn hàng của mình (với tư cách buyer)
- `GET /api/contracts/{id}` — Xem chi tiết
- `POST /api/contracts/{id}/sign` — Ký hợp đồng với tư cách buyer (gắn signed_by_buyer = true)
- `GET /api/contracts/{id}/pdf` — Tải PDF

### Views Only
- `GET /api/assets` — Xem assets
- `GET /api/assets/{id}` — Xem chi tiết
- `GET /api/stores` — Xem store
- `GET /api/subscription-plans` — Xem plan

**Không có quyền:** Tạo/sửa/xoá sản phẩm, upload/sửa/xoá asset, tạo/huỷ subscription

---

## 5. User (Registered) — Người Dùng Thường

Tạo ra qua `POST /api/register`, role = `'user'`, is_active = 0 (cần kích hoạt email).

**Không có bất kỳ quyền nào trên API AgriVerse** vì không được gán Spatie role. Phải được Admin nâng cấp role.

---

## 6. Auth Endpoints (Không Yêu Cầu Auth)

| Method | Endpoint | Mô tả |
|---|---|---|
| POST | /api/register | Đăng ký (name, email, password, repass, phone) |
| POST | /api/login | Đăng nhập → trả về token + user |
| GET | /api/active/{email}/{key} | Kích hoạt tài khoản qua email |
| GET | /api/re-active | Gửi lại email kích hoạt |
| POST | /api/forget-pass | Quên mật khẩu |
| GET | /api/reset-pass/{email}/{key} | Reset mật khẩu |
| PUT | /api/login/google | Đăng nhập Google |
| PUT | /api/login/facebook | Đăng nhập Facebook |

### Auth Endpoints (Yêu Cầu Auth:api)

| Method | Endpoint | Mô tả |
|---|---|---|
| GET | /api/user/detail | Xem thông tin user hiện tại |
| POST | /api/user/update | Cập nhật profile |
| POST | /api/change-pass | Đổi mật khẩu |
| GET | /api/logout | Đăng xuất (revoke token) |

---

## 7. Bảng Permission Mapping

| Permission | Admin | Seller | Employee | Buyer |
|---|---|---|---|---|
| product.view | ✅ | ✅ | ✅ | ✅ |
| product.create | ✅ | ✅ | ✅ | ❌ |
| product.edit | ✅ | ✅ | ✅ | ❌ |
| product.delete | ✅ | ✅ | ❌ | ❌ |
| product.publish | ✅ | ✅ | ❌ | ❌ |
| asset.upload | ✅ | ✅ | ✅ | ❌ |
| asset.view | ✅ | ✅ | ✅ | ✅ |
| asset.edit | ✅ | ✅ | ✅ | ❌ |
| asset.delete | ✅ | ✅ | ❌ | ❌ |
| asset.compress | ✅ | ✅ | ❌ | ❌ |
| order.view | ✅ | ✅ | ❌ | ✅ |
| order.create | ✅ | ✅ | ❌ | ✅ |
| order.edit | ✅ | ✅ | ❌ | ✅ |
| contract.view | ✅ | ✅ | ❌ | ✅ |
| contract.create | ✅ | ✅ | ❌ | ❌ |
| contract.edit | ✅ | ✅ | ❌ | ✅ |
| subscription.view | ✅ | ✅ | ✅ | ❌ |
| subscription.create | ✅ | ❌ | ❌ | ❌ |
| subscription.edit | ✅ | ❌ | ❌ | ❌ |
| subscription.delete | ✅ | ❌ | ❌ | ❌ |
| store.view | ✅ | ✅ | ✅ | ✅ |
| plan.view | ✅ | ✅ | ✅ | ✅ |
| user.view | ✅ | ❌ | ❌ | ❌ |
| user.create | ✅ | ❌ | ❌ | ❌ |
| user.edit | ✅ | ❌ | ❌ | ❌ |
| user.delete | ✅ | ❌ | ❌ | ❌ |
| settings.view | ✅ | ❌ | ❌ | ❌ |
| settings.edit | ✅ | ❌ | ❌ | ❌ |
| dashboard.view | ✅ | ✅ | ✅ | ❌ |
| report.view | ✅ | ❌ | ❌ | ❌ |

---

## 8. API Endpoint Map (AgriVerse)

### Products
| Method | URI | Permission |
|---|---|---|
| GET | /api/products | product.view |
| GET | /api/products/{id} | product.view |
| POST | /api/products | product.create |
| PUT/PATCH | /api/products/{id} | product.edit |
| DELETE | /api/products/{id} | product.delete |
| GET | /api/products/{id}/assets | product.view |

### Digital Passport
| Method | URI | Permission |
|---|---|---|
| GET | /api/products/{id}/digital-passport | product.view |
| GET | /api/digital-passport-logs | product.view |
| GET | /api/digital-passport-logs/{id} | product.view |
| POST | /api/digital-passport-logs | product.edit |

### 3D Assets
| Method | URI | Permission |
|---|---|---|
| GET | /api/assets | asset.view |
| GET | /api/assets/{id} | asset.view |
| POST | /api/assets | asset.upload |
| PUT/PATCH | /api/assets/{id} | asset.edit |
| DELETE | /api/assets/{id} | asset.delete |
| POST | /api/assets/{id}/compress | asset.compress |

### Orders
| Method | URI | Permission |
|---|---|---|
| GET | /api/orders | order.view |
| GET | /api/orders/{id} | order.view |
| POST | /api/orders | order.create |
| POST | /api/orders/{id}/cancel | order.edit |

### Contracts
| Method | URI | Permission |
|---|---|---|
| GET | /api/contracts | contract.view |
| GET | /api/contracts/{id} | contract.view |
| POST | /api/contracts | contract.create |
| POST | /api/contracts/{id}/sign | contract.edit |
| GET | /api/contracts/{id}/pdf | contract.view |

### Subscriptions
| Method | URI | Permission |
|---|---|---|
| GET | /api/subscriptions | subscription.view |
| GET | /api/subscriptions/{id} | subscription.view |
| POST | /api/subscriptions | subscription.create |
| POST | /api/subscriptions/{id}/cancel | subscription.edit |

### Stores
| Method | URI | Permission |
|---|---|---|
| GET | /api/stores | store.view |
| GET | /api/stores/{id} | store.view |

### Subscription Plans
| Method | URI | Permission |
|---|---|---|
| GET | /api/subscription-plans | plan.view |
| GET | /api/subscription-plans/{id} | plan.view |
