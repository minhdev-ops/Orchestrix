# AgriVerse Hub: Backend & Business Logic

## 1. Technology Stack
- **Framework:** Laravel (PHP) for building a robust RESTful API.
- **Language:** PHP.
- **Database:** MySQL or PostgreSQL.
- **Infrastructure:** Docker, Ubuntu Server.

## 2. Core Business Logic
- **RBAC (Role-Based Access Control):** 4-tier permission system:
  - Platform Admin
  - Store Owner (Seller)
  - Store Staff
  - Buyer
- **Subscription Engine:** Manages membership tiers (Basic, Pro, Advanced) with limits on 3D model hosting.
- **Commission Engine:** Automated calculation of fees for successful transactions.
- **Digital Passport:** Manages agricultural technical data, maintenance history, and digital certificates for assets.
- **E-Contract Management:** System for creating and storing legally binding electronic contracts to protect both buyers and sellers of high-value assets (Bonsai, Machinery).

## 3. API Design (v1)
### Authentication & RBAC
... (rest of authentication)

### Giao dịch & Hợp đồng (Transactions & Contracts)
- `POST /api/v1/orders/create`: Create a new high-value transaction order.
- `POST /api/v1/contracts/generate`: Generate an E-Contract based on transaction details.
- `GET /api/v1/contracts/{id}`: View and verify electronic contract status.
- `GET /api/v1/admin/commissions`: (Admin only) Monitor and reconcile commission fees from successful transactions.

### Products & 3D Assets
- `GET /api/v1/products`: List products (filter by Bonsai/Machinery).
- `GET /api/v1/products/{id}`: Detailed specs and 3D config.
- `POST /api/v1/seller/assets/upload`: Upload raw 3D files/360 videos (triggers Asset Pipeline).
- `GET /api/v1/assets/{product_id}/3d-model`: Optimized .glb file path for rendering.
- `GET /api/v1/assets/{product_id}/ar-config`: WebXR configuration parameters.
- `GET /api/v1/machinery/{id}/parts`: Structural data for Exploded View.

### Store & Services
- `GET /api/v1/plans`: Subscription plans.
- `POST /api/v1/seller/subscription/subscribe`: Join or upgrade plan.
- `POST /api/v1/services/3d-scan`: Request AI 3D scanning service.
- `GET /api/v1/services/scan-status/{job_id}`: Track scanning progress.

## 4. Data Model Highlights
- **Users Table:** Identity and roles.
- **Products Table:** Descriptions, agricultural specs (JSON), pricing.
- **3D_Assets Table:** Optimized paths, raw file paths, asset types (360_View, WebAR_Model, etc.), optimization status.
- **Orders Table:** Transactions and commission records.
- **Digital_Passport_Logs:** Life-cycle events (watering, maintenance, part replacement).
