# 🚀 Orchestrix - Hệ thống Quản trị Đa nhiệm & Đa dự án (Modular Multi-Project)

## 📌 Tổng quan dự án

**Orchestrix** là một hệ sinh thái Laravel hiện đại, được thiết kế theo kiến trúc **Modular Architecture**. Hệ thống cho phép quản lý nhiều dự án khác nhau (Multi-project) và tích hợp các module chức năng độc lập, giúp việc mở rộng và bảo trì trở nên cực kỳ linh hoạt.

### Công nghệ lõi:
- **Framework:** Laravel 10/11
- **Architecture:** Modular Design (Tách biệt logic theo từng Module)
- **Frontend:** Vite, Blade, TailwindCSS
- **Database:** MySQL / PostgreSQL

---

## 🏗️ 1. Kiến trúc Hệ thống

Hệ thống được chia thành 3 phần chính:

### A. Core & Admin Central (`app/Admin`, `app/Core`)
- Quản lý trung tâm toàn bộ hệ thống.
- Điều khiển việc bật/tắt các Module thông qua `modules.json`.
- Giao diện Admin tập trung để quản lý dữ liệu từ tất cả các module.

### B. Module System (`/Modules`)
Mỗi module là một "mini-application" đầy đủ bao gồm:
- **Controllers:** Xử lý logic nghiệp vụ.
- **Models:** Định nghĩa cấu trúc dữ liệu.
- **Routes:** Định nghĩa các đường dẫn Web/API/Admin.
- **Views:** Giao diện riêng của module.
- **Providers:** Đăng ký module với hệ thống Laravel.

### C. Multi-Project (`/Projects`)
- Cho phép định nghĩa các project khác nhau.
- Mỗi project có thể chọn sử dụng một tập hợp các module cụ thể thông qua `projects.json`.

---

## 🧩 2. Chi tiết các Module hiện có

### 📝 Module: Blog
Hệ thống quản lý bài viết chuyên nghiệp.
- **Chức năng chính:**
    - Quản lý bài viết (Posts) với đầy đủ định dạng.
    - Phân loại theo danh mục (Categories) và thẻ (Tags).
    - Hệ thống bình luận (Comments) tương tác.
- **Models:** `BlogPost`, `BlogCategory`, `BlogComment`, `BlogTag`.

### 💼 Module: Portfolio
Quản lý hồ sơ năng lực và dự án cá nhân/doanh nghiệp.
- **Chức năng chính:**
    - Quản lý thông tin giới thiệu (About), kỹ năng (Skills).
    - Trưng bày các dự án đã thực hiện (Projects).
    - Quản lý thông tin liên hệ (Contact) và cấu hình (Settings).
    - Thống kê thành tựu (Stats) và kinh nghiệm (Experience).
- **Models:** `Project`, `Skill`, `About`, `AboutExperience`, `Contact`.

---

## 🛠️ 3. Hướng dẫn Phát triển

### Cách tạo Module mới
Sử dụng command artisan đã được tối ưu hóa:
```bash
php artisan make:module {TenModule}
```
Lệnh này sẽ tự động tạo cấu trúc thư mục tiêu chuẩn trong `/Modules`.

### Đăng ký Module
1. Sau khi tạo, hãy kiểm tra file `Modules/{TenModule}/Providers/{TenModule}ServiceProvider.php`.
2. Hệ thống sẽ tự động load Provider này thông qua logic trong `AppServiceProvider`.
3. Bật module trong `modules.json`:
```json
{
    "{ten_module}": true
}
```

### Đăng ký Module vào Dashboard Admin
Để module hiển thị trên menu và dashboard của Admin, bạn cần đăng ký nó trong `app/Providers/AppServiceProvider.php`:

```php
$moduleManager = app(\App\Services\ModuleManagerService::class);

$moduleManager->registerModule([
    'id' => 'ten-module',           // ID duy nhất
    'name' => 'Tên Hiển Thị',      // Tên hiển thị trên Menu
    'icon' => 'icon_name',         // Tên icon từ Material Symbols
    'route' => 'admin.ten-module.index', // Route trang chính của module
    'description' => 'Mô tả ngắn'  // Hiển thị ở Dashboard
]);
```

### Quy tắc đặt tên (Naming Convention)
- **Controller:** `{Name}Controller.php`
- **Model:** `{ModuleName}{Entity}.php` (VD: `BlogCategory.php`)
- **Route:** Chia tách `web.php`, `api.php`, và `admin.php`.

---

## ⚙️ 4. Quản lý Dự án (Multi-Tenant)

Cấu hình dự án tại `projects.json`:
```json
{
  "Project_A": {
    "domain": "project-a.test",
    "modules": ["Blog", "Portfolio"]
  },
  "Project_B": {
    "domain": "project-b.test",
    "modules": ["Blog"]
  }
}
```
Hệ thống sẽ tự động nhận diện domain và chỉ load các module được cấp phép cho project đó.

---

## 🚀 5. Lộ trình phát triển (Roadmap)
- [ ] Xây dựng UI Admin tổng thể (Dashboard).
- [ ] Tích hợp hệ thống phân quyền (ACL) theo từng module.
- [ ] Phát triển CRUD Generator tự động cho Module mới.
- [ ] Hỗ trợ Multi-database cho từng dự án để đảm bảo an toàn dữ liệu.

---
*Tài liệu được biên soạn cho dự án Orchestrix - 2026*
