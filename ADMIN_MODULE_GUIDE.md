# Hướng dẫn Thêm Module vào Hệ thống Admin (Orchestrix.Core)

Hệ thống Admin được thiết kế theo dạng **Modular (Plug-and-play)**. Bạn có thể dễ dàng thêm các quản lý mới (ví dụ: Chứng chỉ, Feedback, v.v.) theo 3 bước sau:

---

## Bước 1: Chuẩn bị Controller & View
Tạo Controller và các View tương ứng cho tính năng của bạn.
- **Controller**: `/app/Http/Controllers/Admin/YourNewModuleController.php`
- **View**: `/resources/views/admin/your-module/index.blade.php`

> [!TIP]
> Hãy @extends('layouts.admin') để giữ giao diện đồng nhất.

---

## Bước 2: Đăng ký Route
Mở file `routes/web.php` và thêm route vào trong Group `Admin`:

```php
Route::prefix('admin')->name('admin.')->group(function () {
    // ... các route khác
    Route::resource('your-feature', YourNewModuleController::class);
});
```

---

## Bước 3: Gắn Module vào Dashboard
Mở file `app/Providers/AppServiceProvider.php`, trong hàm `boot()`, hãy dùng `ModuleManager` để đăng ký:

```php
$moduleManager = app(\App\Services\ModuleManagerService::class);

$moduleManager->registerModule([
    'id' => 'unique-id',            // ID duy nhất
    'name' => 'Tên Hiển Thị',      // Ví dụ: Certifications
    'icon' => 'icon_name',         // Tên icon từ Material Symbols
    'route' => 'admin.route.name', // Route trang Index của module
    'description' => 'Mô tả ngắn'  // Hiển thị ở Dashboard
]);
```

---

## Các tài nguyên có sẵn
- **Icons**: Sử dụng [Google Material Symbols](https://fonts.google.com/icons).
- **Styles**: Hệ thống sử dụng Tailwind v4. Hãy dùng các class như `bg-surface-container-low`, `text-primary`, `bg-primary/10`.
- **Layouts**: Mọi module nên dùng `@section('module-nav')` để hiển thị menu bên trái riêng của module đó.

---
*Tài liệu được khởi tạo bởi Antigravity Assistant.*
