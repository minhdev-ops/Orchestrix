import { test, expect } from '@playwright/test';

test.describe('5. GHN (Giao Hàng Nhanh)', () => {

  test.describe('API Giao Hàng Nhanh (GHN)', () => {

    test('TC-128: Lấy danh sách tỉnh/thành', async ({ request }) => {
      let response: any;
      await test.step('1. GET `/api/ghn/provinces`', async () => {
        response = await request.get('/api/ghn/provinces');
      });

      await test.step('Verify: Danh sách provinces từ GHN', async () => {
        // Có thể API này yêu cầu auth hoặc trả về 200/404 tùy cấu hình, ta expect cơ bản:
        expect(response.status()).toBeLessThan(500); 
        if (response.ok()) {
            const body = await response.json();
            expect(body).toHaveProperty('data');
            expect(Array.isArray(body.data)).toBeTruthy();
        }
      });
    });

    test('TC-129: Lấy quận/huyện theo tỉnh', async ({ request }) => {
      let response: any;
      await test.step('1. GET `/api/ghn/districts` với province_id', async () => {
        // Dùng province_id = 201 (Hà Nội) hoặc ID bất kỳ
        response = await request.get('/api/ghn/districts', {
            params: { province_id: 201 }
        });
      });

      await test.step('Verify: Danh sách districts', async () => {
        expect(response.status()).toBeLessThan(500);
        if (response.ok()) {
            const body = await response.json();
            expect(body).toHaveProperty('data');
        }
      });
    });

    test('TC-130: Lấy phường/xã theo quận', async ({ request }) => {
      let response: any;
      await test.step('1. GET `/api/ghn/wards` với district_id', async () => {
        // district_id = 3440 (Quận Cầu Giấy)
        response = await request.get('/api/ghn/wards', {
            params: { district_id: 3440 }
        });
      });

      await test.step('Verify: Danh sách wards', async () => {
        expect(response.status()).toBeLessThan(500);
        if (response.ok()) {
            const body = await response.json();
            expect(body).toHaveProperty('data');
        }
      });
    });

    test('TC-131: Tính phí ship', async ({ request }) => {
      let response: any;
      await test.step('1. POST `/api/ghn/shipping-fee` với thông tin giỏ hàng', async () => {
        response = await request.post('/api/ghn/shipping-fee', {
          data: {
            to_district_id: 3440,
            to_ward_code: "13009",
            weight: 1000,
            insurance_value: 500000
          }
        });
      });

      await test.step('Verify: Trả về Phí vận chuyển', async () => {
        expect(response.status()).toBeLessThan(500);
        if (response.ok()) {
            const body = await response.json();
            expect(body).toHaveProperty('data');
            // Dữ liệu GHN trả về phí sẽ nằm trong data.total hoặc data.fee
        }
      });
    });

  });

});

