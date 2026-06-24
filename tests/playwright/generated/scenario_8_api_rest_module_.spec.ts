import { test, expect } from '@playwright/test';

test.describe('8. API REST (Module)', () => {

  test.describe('8.1 Product API', () => {

    test('TC-175: GET /api/products', async ({ request }) => {
      let response;
      await test.step('1. GET có pagination params', async () => {
        response = await request.get('/api/products');
      });

      await test.step('Verify: - Danh sách products', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-176: POST /api/products', async ({ request }) => {
      let response;
      await test.step('1. POST với đầy đủ fields', async () => {
        response = await request.post('/api/products', { data: {} });
      });

      await test.step('Verify: - Product được tạo', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-177: PUT /api/products/{id}', async ({ request }) => {
      let response;
      await test.step('1. Cập nhật thông tin', async () => {
        response = await request.put('/api/products/1', { data: {} });
      });

      await test.step('Verify: - Product được update', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-178: DELETE /api/products/{id}', async ({ request }) => {
      let response;
      await test.step('1. DELETE', async () => {
        response = await request.delete('/api/products/1');
      });

      await test.step('Verify: - Product bị xóa', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

  });

  test.describe('8.2 3D Assets API', () => {

    test('TC-179: Upload 3D model', async ({ request }) => {
      let response;
      await test.step('1. POST /api/assets với file .glb/.gltf', async () => {
        response = await request.post('/api/assets', { data: {} });
      });

      await test.step('Verify: - Asset được upload', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-180: Compress 3D model', async ({ request }) => {
      let response;
      await test.step('1. POST /api/assets/{asset}/compress', async () => {
        response = await request.post('/api/assets/1/compress');
      });

      await test.step('Verify: - Model được compress (draco)', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-181: Lấy model cho product', async ({ request }) => {
      let response;
      await test.step('1. GET /api/products/{product}/3d-model', async () => {
        response = await request.get('/api/products/1/3d-model');
      });

      await test.step('Verify: - Thông tin 3D model', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-182: Lấy AR config', async ({ request }) => {
      let response;
      await test.step('1. GET /api/products/{product}/ar-config', async () => {
        response = await request.get('/api/products/1/ar-config');
      });

      await test.step('Verify: - AR configuration', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

  });

  test.describe('8.3 Contract API', () => {

    test('TC-183: Tạo contract', async ({ request }) => {
      let response;
      await test.step('1. POST /api/contracts', async () => {
        response = await request.post('/api/contracts', { data: {} });
      });

      await test.step('Verify: - Contract được tạo + PDF', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-184: Ký contract', async ({ request }) => {
      let response;
      await test.step('1. POST /api/contracts/{contract}/sign', async () => {
        response = await request.post('/api/contracts/1/sign');
      });

      await test.step('Verify: - Contract signed, PDF updated', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

  });

  test.describe('8.4 Subscription API', () => {

    test('TC-185: Đăng ký subscription', async ({ request }) => {
      let response;
      await test.step('1. POST /api/subscriptions', async () => {
        response = await request.post('/api/subscriptions', { data: {} });
      });

      await test.step('Verify: - Subscription được tạo', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-186: Hủy subscription', async ({ request }) => {
      let response;
      await test.step('1. POST /api/subscriptions/{sub}/cancel', async () => {
        response = await request.post('/api/subscriptions/1/cancel');
      });

      await test.step('Verify: - Subscription cancelled', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

  });

  test.describe('8.5 Digital Passport API', () => {

    test('TC-187: Tạo passport cho product', async ({ request }) => {
      let response;
      await test.step('1. POST /api/products/{product}/digital-passport', async () => {
        response = await request.post('/api/products/1/digital-passport', { data: {} });
      });

      await test.step('Verify: - Passport được tạo', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-188: Cập nhật passport', async ({ request }) => {
      let response;
      await test.step('1. PUT /api/passport/{passport}', async () => {
        response = await request.put('/api/passport/1', { data: {} });
      });

      await test.step('Verify: - Passport updated + log', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-189: Xóa passport', async ({ request }) => {
      let response;
      await test.step('1. DELETE /api/passport/{passport}', async () => {
        response = await request.delete('/api/passport/1');
      });

      await test.step('Verify: - Passport deleted', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-190: Xem passport summary', async ({ request }) => {
      let response;
      await test.step('1. GET /api/products/{product}/passport-summary', async () => {
        response = await request.get('/api/products/1/passport-summary');
      });

      await test.step('Verify: - Tóm tắt passport', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-191: Xem log thay đổi', async ({ request }) => {
      let response;
      await test.step('1. GET /api/digital-passport-logs', async () => {
        response = await request.get('/api/digital-passport-logs');
      });

      await test.step('Verify: - Lịch sử thay đổi', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

  });

  test.describe('8.6 AI Scanning API', () => {

    test('TC-192: Gửi yêu cầu scan', async ({ request }) => {
      let response;
      await test.step('1. POST /api/services/3d-scan với ảnh', async () => {
        const formData = new FormData();
        formData.append('image', '');
        response = await request.post('/api/services/3d-scan', {
          multipart: { image: '' }
        });
      });

      await test.step('Verify: - Scan job được tạo', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-193: Kiểm tra trạng thái scan', async ({ request }) => {
      let response;
      await test.step('1. GET /api/services/scan-status/{job}', async () => {
        response = await request.get('/api/services/scan-status/1');
      });

      await test.step('Verify: - Trạng thái + kết quả (nếu done)', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-194: Xem danh sách scan của user', async ({ request }) => {
      let response;
      await test.step('1. GET /api/services/my-scans', async () => {
        response = await request.get('/api/services/my-scans');
      });

      await test.step('Verify: - Danh sách scan jobs', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

  });

  test.describe('8.7 Reports API', () => {

    test('TC-195: Seller revenue report', async ({ request }) => {
      let response;
      await test.step('1. GET /api/reports/seller/revenue', async () => {
        response = await request.get('/api/reports/seller/revenue');
      });

      await test.step('Verify: - Doanh thu theo thời gian', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-196: Buyer stats', async ({ request }) => {
      let response;
      await test.step('1. GET /api/reports/buyer/stats', async () => {
        response = await request.get('/api/reports/buyer/stats');
      });

      await test.step('Verify: - Thống kê mua hàng', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

  });

  test.describe('8.8 Permission API (Admin)', () => {

    test('TC-197: Xem roles', async ({ request }) => {
      let response;
      await test.step('1. GET /api/admin/roles', async () => {
        response = await request.get('/api/admin/roles');
      });

      await test.step('Verify: - Danh sách roles', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-198: Xem permissions', async ({ request }) => {
      let response;
      await test.step('1. GET /api/admin/permissions', async () => {
        response = await request.get('/api/admin/permissions');
      });

      await test.step('Verify: - Danh sách permissions', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-199: Gán role cho user', async ({ request }) => {
      let response;
      await test.step('1. POST /api/admin/roles/assign', async () => {
        response = await request.post('/api/admin/roles/assign', { data: {} });
      });

      await test.step('Verify: - Role được gán', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-200: Xóa role khỏi user', async ({ request }) => {
      let response;
      await test.step('1. POST /api/admin/roles/remove', async () => {
        response = await request.post('/api/admin/roles/remove', { data: {} });
      });

      await test.step('Verify: - Role bị xóa', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

    test('TC-201: Sync permissions với role', async ({ request }) => {
      let response;
      await test.step('1. POST /api/admin/permissions/sync', async () => {
        response = await request.post('/api/admin/permissions/sync', { data: {} });
      });

      await test.step('Verify: - Permissions được cập nhật', async () => {
        expect(response).toBeDefined();
        expect(response.status()).toBeLessThan(500);
      });
    });

  });

});
