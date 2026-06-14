# AgriVerse Hub: Business Requirements & User Stories

## 1. Business Requirements
- **Multi-tier Access:** Support complex data flow and permissions for Admin, Sellers (Gardeners/Machinery companies), Staff, and Buyers.
- **Zero-Installation Experience:** Entirely web-based (Desktop and Mobile) to eliminate app download barriers.
- **3D Performance Optimization:** Guarantee < 3s load time on 4G networks.
- **In-depth Interaction:** Specific 3D tools for each product category (AR for plants, Exploded View for machines).
- **Multi-channel Monetization:** Subscription plans, 3D scanning service fees, and transaction commissions.
- **Digital Passport:** Record "digital life-cycle" data (watering history, maintenance, certification) to increase asset value.
- **E-Contract (Legal Security):** Ensure the creation and secure storage of legally binding electronic contracts for high-value agricultural assets.

## 2. Main Use Cases
...
- **UC-B04 (Buyer):** Execute transactions and sign E-Contracts under platform protection.
- **UC-B05 (Buyer):** Real-time chat/consultation (RAG-enhanced).
...
- **UC-A02 (Admin):** Financial reconciliation (subscription revenue, commissions).

## 3. Technical Requirements (Standards)
- **Fallback Support:** Must provide high-quality 2D alternatives for devices lacking WebGL/WebAR support to maintain 100% accessibility.
- **Performance:** Maintain < 3s load time via Draco compression and LOD (Level of Detail).

## 3. User Stories
| ID | Role | Desire | Benefit |
|----|------|--------|---------|
| S-01 | Buyer | Rotate 360 & zoom 3D models | Understand shape and structure without visiting in person. |
| S-02 | Buyer | Use WebAR for fitting | Evaluate aesthetic compatibility with their garden/home. |
| S-03 | Buyer | View Exploded View & animations | Understand complex machine operation before investing. |
| S-04 | Seller | Use AI Scanning service | Easily digitize products without specialized equipment. |
| S-05 | Seller | Manage specs/finances via simple UI | Operate the store without advanced 3D knowledge. |
| S-06 | Admin | Manage subscriptions & commissions | Maintain sustainable platform revenue. |
