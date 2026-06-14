# AgriVerse Hub: API Specification (v1)

## 1. General Info
- **Base URL:** `/api/v1`
- **Format:** JSON
- **Auth:** Bearer Token (JWT).

## 2. Key Endpoints
### 2.1. Products & 3D
- `GET /products`: List with filters (category, price range, store).
- `GET /products/{id}`: Detailed specs + `3d_config` + `digital_passport_history`.
- `POST /seller/assets/upload`: Upload asset for processing. 
    * *Payload:* `file` (raw 3D or video), `asset_type` (360_view, ar_model, etc.).

### 2.2. AI Scanning
- `POST /services/3d-scan`: Submit video for reconstruction.
    * *Payload:* `source_video_url`, `priority` (based on subscription).
- `GET /services/scan-status/{job_id}`: Poll status (Pending, Processing, Completed).

### 2.3. Transactions & Legal
- `POST /orders/create`: Initiate purchase.
- `GET /contracts/{order_id}`: Retrieve E-Contract for review.
- `POST /contracts/sign`: Handle digital signature (using cryptographic hash).

### 2.4. Admin
- `GET /admin/stats`: Marketplace overview.
- `GET /admin/commissions`: Financial reconciliation report.
