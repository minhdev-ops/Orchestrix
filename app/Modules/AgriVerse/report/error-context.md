# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: generated/scenario_1_authentication_account.spec.ts >> 1. Authentication & Account >> 1.2 Đăng nhập >> TC-07: Đăng nhập với sai mật khẩu
- Location: tests/playwright/generated/scenario_1_authentication_account.spec.ts:100:5

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: locator('.login-error')
Expected: visible
Timeout: 5000ms
Error: element(s) not found

Call log:
  - Expect "toBeVisible" with timeout 5000ms
  - waiting for locator('.login-error')

```

# Page snapshot

```yaml
- generic [ref=e5]:
  - link "eco AgriVerse" [ref=e6] [cursor=pointer]:
    - /url: http://localhost:8000/agriverse
    - generic [ref=e8]: eco
    - generic [ref=e9]: AgriVerse
  - heading "Đăng nhập" [level=1] [ref=e10]
  - paragraph [ref=e11]: Chào mừng trở lại AgriVerse
  - generic [ref=e12]:
    - generic [ref=e13]:
      - generic [ref=e14]: Email
      - textbox "your@email.com" [ref=e15]
    - generic [ref=e16]:
      - generic [ref=e17]: Mật khẩu
      - textbox "••••••••" [ref=e18]
    - generic [ref=e19]:
      - generic [ref=e20] [cursor=pointer]:
        - checkbox "Ghi nhớ" [ref=e21]
        - generic [ref=e22]: Ghi nhớ
      - link "Quên mật khẩu?" [ref=e23] [cursor=pointer]:
        - /url: /password/reset
    - button "Đăng nhập" [ref=e24] [cursor=pointer]
  - generic [ref=e25]:
    - text: Chưa có tài khoản?
    - link "Đăng ký" [ref=e26] [cursor=pointer]:
      - /url: http://localhost:8000/register
  - generic [ref=e27]:
    - generic [ref=e28]: Tài khoản dùng thử
    - generic [ref=e29]:
      - generic [ref=e30]: "Admin: admin@orchestrix.com"
      - generic [ref=e31]: "Seller: seller@orchestrix.com"
      - generic [ref=e32]: "Buyer: buyer@orchestrix.com"
      - generic [ref=e33]: "Mật khẩu: 12345678"
```

# Test source

```ts
  12  |         // TODO: Implement interaction
  13  |       });
  14  |       await test.step('3. Submit', async () => {
  15  |         // TODO: Implement interaction
  16  |       });
  17  | 
  18  |       // Expected Results:
  19  |       // - Redirect về trang chủ, user được tạo trong DB, email activation được gửi
  20  |       // TODO: Implement assertions
  21  |     });
  22  | 
  23  |     test('TC-02: Đăng ký với email đã tồn tại', async ({ page }) => {
  24  |       await test.step('1. Nhập email đã có trong hệ thống', async () => {
  25  |         // TODO: Implement interaction
  26  |       });
  27  |       await test.step('2. Submit', async () => {
  28  |         // TODO: Implement interaction
  29  |       });
  30  | 
  31  |       // Expected Results:
  32  |       // - Báo lỗi "Email đã được sử dụng"
  33  |       // TODO: Implement assertions
  34  |     });
  35  | 
  36  |     test('TC-03: Đăng ký với password quá ngắn (< 8 ký tự)', async ({ page }) => {
  37  |       await test.step('1. Nhập password 6 ký tự', async () => {
  38  |         // TODO: Implement interaction
  39  |       });
  40  |       await test.step('2. Submit', async () => {
  41  |         // TODO: Implement interaction
  42  |       });
  43  | 
  44  |       // Expected Results:
  45  |       // - Báo lỗi validation password
  46  |       // TODO: Implement assertions
  47  |     });
  48  | 
  49  |     test('TC-04: Đăng ký với confirm password không khớp', async ({ page }) => {
  50  |       await test.step('1. Nhập password/confirm khác nhau', async () => {
  51  |         // TODO: Implement interaction
  52  |       });
  53  |       await test.step('2. Submit', async () => {
  54  |         // TODO: Implement interaction
  55  |       });
  56  | 
  57  |       // Expected Results:
  58  |       // - Báo lỗi confirm password không khớp
  59  |       // TODO: Implement assertions
  60  |     });
  61  | 
  62  |   });
  63  | 
  64  |   test.describe('1.2 Đăng nhập', () => {
  65  | 
  66  |     test('TC-05: Đăng nhập thành công (user thường)', async ({ page }) => {
  67  |       await test.step('1. Vào `/login`', async () => {
  68  |         await page.goto('/login');
  69  |       });
  70  |       await test.step('2. Nhập email/password đúng', async () => {
  71  |         await page.fill('input[type="email"]', 'buyer@orchestrix.com');
  72  |         await page.fill('input[type="password"]', '12345678');
  73  |       });
  74  |       await test.step('3. Submit', async () => {
  75  |         await page.click('button[type="submit"]');
  76  |       });
  77  | 
  78  |       await test.step('Verify: Redirect về `/agriverse/`', async () => {
  79  |         await page.waitForURL(/agriverse/);
  80  |         await expect(page).toHaveURL(/agriverse/);
  81  |       });
  82  |     });
  83  | 
  84  |     test('TC-06: Đăng nhập thành công (admin)', async ({ page }) => {
  85  |       await page.goto('/login');
  86  |       await test.step('1. Nhập email/password admin', async () => {
  87  |         await page.fill('input[type="email"]', 'admin@orchestrix.com');
  88  |         await page.fill('input[type="password"]', '12345678');
  89  |       });
  90  |       await test.step('2. Submit', async () => {
  91  |         await page.click('button[type="submit"]');
  92  |       });
  93  | 
  94  |       await test.step('Verify: Redirect về `/admin/agriverse`', async () => {
  95  |         await page.waitForURL(/admin\/agriverse/);
  96  |         await expect(page).toHaveURL(/admin\/agriverse/);
  97  |       });
  98  |     });
  99  | 
  100 |     test('TC-07: Đăng nhập với sai mật khẩu', async ({ page }) => {
  101 |       await page.goto('/login');
  102 |       await test.step('1. Nhập sai password', async () => {
  103 |         await page.fill('input[type="email"]', 'buyer@orchestrix.com');
  104 |         await page.fill('input[type="password"]', 'wrongpassword');
  105 |       });
  106 |       await test.step('2. Submit', async () => {
  107 |         await page.click('button[type="submit"]');
  108 |       });
  109 | 
  110 |       await test.step('Verify: Báo lỗi "Thông tin đăng nhập không đúng"', async () => {
  111 |         const errorMsg = page.locator('.login-error');
> 112 |         await expect(errorMsg).toBeVisible();
      |                                ^ Error: expect(locator).toBeVisible() failed
  113 |       });
  114 |     });
  115 | 
  116 |     test('TC-08: Đăng nhập với email chưa kích hoạt', async ({ page }) => {
  117 |       await test.step('1. Dùng email chưa active', async () => {
  118 |         // TODO: Implement interaction
  119 |       });
  120 |       await test.step('2. Submit', async () => {
  121 |         // TODO: Implement interaction
  122 |       });
  123 | 
  124 |       // Expected Results:
  125 |       // - Báo lỗi "Tài khoản chưa được kích hoạt"
  126 |       // TODO: Implement assertions
  127 |     });
  128 | 
  129 |   });
  130 | 
  131 |   test.describe('1.3 Kích hoạt tài khoản (Email Activation)', () => {
  132 | 
  133 |     test('TC-09: Kích hoạt với link hợp lệ', async ({ page }) => {
  134 |       await test.step('1. Click link trong email activation', async () => {
  135 |         // TODO: Implement interaction
  136 |       });
  137 |       await test.step('2. GET `/api/active/{email}/{key}`', async () => {
  138 |         // TODO: Implement interaction
  139 |       });
  140 | 
  141 |       // Expected Results:
  142 |       // - User được active, thông báo thành công
  143 |       // TODO: Implement assertions
  144 |     });
  145 | 
  146 |     test('TC-10: Kích hoạt với key sai', async ({ page }) => {
  147 |       await test.step('1. Sửa key trong link', async () => {
  148 |         // TODO: Implement interaction
  149 |       });
  150 |       await test.step('2. GET API', async () => {
  151 |         // await page.request.get('API');
  152 |         // TODO: Implement interaction
  153 |       });
  154 | 
  155 |       // Expected Results:
  156 |       // - Báo lỗi key không hợp lệ
  157 |       // TODO: Implement assertions
  158 |     });
  159 | 
  160 |     test('TC-11: Gửi lại email kích hoạt', async ({ page }) => {
  161 |       await test.step('1. GET `/api/re-active` với email', async () => {
  162 |         // TODO: Implement interaction
  163 |       });
  164 |       await test.step('2. Kiểm tra log mail', async () => {
  165 |         // TODO: Implement interaction
  166 |       });
  167 | 
  168 |       // Expected Results:
  169 |       // - Email activation mới được gửi
  170 |       // TODO: Implement assertions
  171 |     });
  172 | 
  173 |   });
  174 | 
  175 |   test.describe('1.4 Quên mật khẩu', () => {
  176 | 
  177 |     test('TC-12: Gửi yêu cầu reset password', async ({ page }) => {
  178 |       await test.step('1. POST `/api/forget-pass` với email', async () => {
  179 |         // TODO: Implement interaction
  180 |       });
  181 |       await test.step('2. Kiểm tra log mail', async () => {
  182 |         // TODO: Implement interaction
  183 |       });
  184 | 
  185 |       // Expected Results:
  186 |       // - Email reset password được gửi
  187 |       // TODO: Implement assertions
  188 |     });
  189 | 
  190 |     test('TC-13: Reset password thành công', async ({ page }) => {
  191 |       await test.step('1. Click link trong email', async () => {
  192 |         // TODO: Implement interaction
  193 |       });
  194 |       await test.step('2. GET `/api/reset-pass/{email}/{key}`', async () => {
  195 |         // TODO: Implement interaction
  196 |       });
  197 | 
  198 |       // Expected Results:
  199 |       // - Password được reset, thông báo thành công
  200 |       // TODO: Implement assertions
  201 |     });
  202 | 
  203 |   });
  204 | 
  205 |   test.describe('1.5 Đăng nhập mạng xã hội', () => {
  206 | 
  207 |     test('TC-14: Đăng nhập Google thành công', async ({ page }) => {
  208 |       await test.step('1. PUT `/api/login/google` với token Google', async () => {
  209 |         // TODO: Implement interaction
  210 |       });
  211 | 
  212 |       // Expected Results:
```