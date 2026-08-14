#!/usr/bin/env python3
"""Convert IT-Challenge document to .docx with proper formatting."""

from docx import Document
from docx.shared import Pt, Inches, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml.ns import qn
import re

doc = Document()

# --- Styles ---
style = doc.styles['Normal']
style.font.name = 'Times New Roman'
style.font.size = Pt(12)
style.paragraph_format.line_spacing = 1.5
style.paragraph_format.space_after = Pt(6)

for level in range(1, 4):
    hs = doc.styles[f'Heading {level}']
    hs.font.name = 'Times New Roman'
    hs.font.color.rgb = RGBColor(0, 0, 0)
    if level == 1:
        hs.font.size = Pt(16)
        hs.font.bold = True
    elif level == 2:
        hs.font.size = Pt(14)
        hs.font.bold = True
    else:
        hs.font.size = Pt(13)
        hs.font.bold = True

def add_centered(text, size=12, bold=False):
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run(text)
    run.font.size = Pt(size)
    run.bold = bold
    return p

def add_table(headers, rows):
    table = doc.add_table(rows=1 + len(rows), cols=len(headers))
    table.style = 'Table Grid'
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    for i, h in enumerate(headers):
        cell = table.rows[0].cells[i]
        cell.text = h
        for p in cell.paragraphs:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            for r in p.runs:
                r.bold = True
                r.font.size = Pt(11)
    for ri, row in enumerate(rows):
        for ci, val in enumerate(row):
            cell = table.rows[ri + 1].cells[ci]
            cell.text = str(val)
            for p in cell.paragraphs:
                for r in p.runs:
                    r.font.size = Pt(11)
    return table

# ============================================================
# HEADER
# ============================================================
add_centered('BỘ GIÁO DỤC VÀ ĐÀO TẠO', 14, True)
add_centered('TRƯỜNG ĐẠI HỌC NÔNG LÂM TP HCM', 14, True)
add_centered('KHOA CÔNG NGHỆ THÔNG TIN', 13, True)
doc.add_paragraph()
add_centered('BẢN THUYẾT MINH', 16, True)
add_centered('MÔ HÌNH, SẢN PHẨM THAM DỰ CUỘC THI', 13, True)
add_centered('"IT-CHALLENGE" LẦN II NĂM 2026', 13, True)
doc.add_paragraph()
add_centered('Kính gửi: BTC CUỘC THI "IT-CHALLENGE"', 12, False)
add_centered('(Khoa Công nghệ Thông tin, Trường Đại học Nông Lâm Tp. Hồ Chí Minh)', 11, False)
doc.add_paragraph()
add_centered('NGHIÊN CỨU VÀ XÂY DỰNG HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ', 14, True)
add_centered('CÂY CẢNH NGHỆ THUẬT TÍCH HỢP CÔNG NGHỆ 3D', 14, True)
add_centered('VÀ TRUY XUẤT NGUỒN GỐC SỐ', 14, True)
doc.add_paragraph()

# Members
add_centered('Danh Sách Thành Viên', 13, True)
add_table(
    ['', 'HỌ VÀ TÊN', 'MSSV', 'LỚP', 'EMAIL', 'SĐT'],
    [
        ['Nhóm trưởng', 'Nguyễn Quang Minh', '23130193', 'DH23DTC', 'nguyenquangminhitnlu@gmail.com', '0933194203'],
        ['Thành viên 2', '—', '24130029', 'DH24DTA', 'nguye0707@gmail.com', '0394360026'],
    ]
)
doc.add_page_break()

# ============================================================
# 1. TÓM TẮT SẢN PHẨM (Customer-facing)
# ============================================================
doc.add_heading('1. Tóm tắt sản phẩm', level=1)

doc.add_heading('1.1. Tổng quan sản phẩm', level=2)
doc.add_paragraph(
    'AgriVerse là nền tảng thương mại điện tử thế hệ mới, chuyên biệt cho thị trường giao dịch '
    'cây cảnh nghệ thuật và bonsai cao cấp. Hệ thống không chỉ là sàn mua bán trực tuyến mà còn '
    'là hệ sinh thái toàn diện: trưng bày sản phẩm bằng mô hình 3D xoay 360°, truy xuất nguồn gốc '
    'qua Hộ chiếu số, kết nối cộng đồng qua diễn đàn, chẩn đoán bệnh cây bằng AI, và theo dõi '
    'sự phát triển cây trong vườn cá nhân.'
)

doc.add_heading('1.2. Mô tả ngắn gọn', level=2)
doc.add_paragraph(
    'AgriVerse là sàn giao dịch đa người bán (Multi-vendor Marketplace) dành riêng cho cây cảnh '
    'nghệ thuật. Người bán có thể trưng bày tác phẩm bonsai dưới dạng mô hình 3D tương tác 360°. '
    'Người mua có thể xem chi tiết từng cành lá, tra cứu lịch sử phát triển cây, giao tiếp trực tiếp '
    'với người bán, và tham gia cộng đồng chia sẻ kiến thức chăm sóc cây.'
)

doc.add_heading('1.3. Đối tượng sử dụng', level=2)
doc.add_paragraph('Người mua (Người sưu tầm): Tìm kiếm và sở hữu bonsai đẹp, rõ nguồn gốc. Học hỏi kỹ thuật chăm sóc từ cộng đồng. So sánh sản phẩm thông minh và theo dõi đơn hàng real-time.', style='List Bullet')
doc.add_paragraph('Người bán (Nghệ nhân, Nhà vườn): Trưng bày chuyên nghiệp với mô hình 3D. Quản lý cửa hàng, đơn hàng, chat trực tiếp với khách. Tiếp cận đúng tệp khách hàng tiềm năng.', style='List Bullet')
doc.add_paragraph('Quản trị viên & Chuyên gia: Vận hành hệ thống, kiểm duyệt nội dung, xử lý giao dịch, hỗ trợ chẩn đoán bệnh lý thực vật.', style='List Bullet')

doc.add_heading('1.4. Lợi ích nổi bật', level=2)
benefits = [
    ('Xem sản phẩm chân thực qua 3D & AR', 'Không còn lo "mua hàng qua ảnh". Mô hình 3D xoay 360° và AR giúp đặt cây ảo vào không gian thật.'),
    ('Bảo chứng nguồn gốc bằng Hộ chiếu số', 'Mỗi tác phẩm bonsai được cấp hộ chiếu số lưu trữ toàn bộ lịch sử: quá trình tạo dáng, lịch sử sang tên, giải thưởng.'),
    ('Giao tiếp trực tiếp với người bán', 'Chat real-time ngay trên nền tảng — trao đổi về tình trạng cây, phương thức thanh toán.'),
    ('Vận chuyển tự động & minh bạch', 'Hệ thống tự động tính phí vận chuyển real-time theo mã tỉnh → huyện → xã qua GHTK.'),
    ('Cộng đồng tri thức chuyên sâu', 'Diễn đàn thảo luận, thư viện 20+ bài viết chăm sóc cây, vườn cá nhân, AI chẩn đoán bệnh.'),
    ('So sánh thông minh', 'Đặt nhiều sản phẩm lên bảng so sánh side-by-side — xem thông số, giá, đánh giá.'),
]
for title, desc in benefits:
    doc.add_paragraph(f'{title}: {desc}', style='List Bullet')

doc.add_page_break()

# ============================================================
# 2. LÝ DO CHỌN ĐỀ TÀI (Customer-facing)
# ============================================================
doc.add_heading('2. Lý do chọn đề tài', level=1)
doc.add_paragraph(
    'Thị trường bonsai Việt Nam và thế giới có giá trị kinh tế lớn nhưng bị bỏ ngỏ trong chuyển đổi số. '
    'Mua bán trên mạng xã hội hoặc sàn TMĐT đại trà gặp nhiều rào cản:'
)
reasons = [
    '"Treo đầu dê bán thịt chó": Ảnh 2D dễ chỉnh sửa, không thể hiện chiều sâu → người mua dễ bị lừa. AgriVerse giải quyết bằng mô hình 3D 360° và AR.',
    'Mất mát thông tin quý: Bonsai mất hàng chục năm tạo hình qua nhiều đời chủ, nhưng thông tin chỉ truyền miệng → khó định giá. AgriVerse giải quyết bằng Hộ chiếu số.',
    'Thiếu kênh tri thức: Người chơi mới khó tiếp cận kỹ thuật chăm sóc → tỷ lệ cây chết sau mua cao. AgriVerse giải quyết bằng thư viện Journal và diễn đàn Forum.',
    'Vận chuyển đặc thù: Cây sống cần quy trình đóng gói riêng. AgriVerse tích hợp GHTK tính phí theo mã tỉnh → huyện → xã.',
    'Thiếu bảo vệ giao dịch: Không có hợp đồng, hoàn tiền hay escrow. AgriVerse xây dựng E-Contract, Refund workflow, xác thực 2FA.',
]
for r in reasons:
    doc.add_paragraph(r, style='List Bullet')

doc.add_page_break()

# ============================================================
# 3. CHỨC NĂNG SẢN PHẨM (Customer-facing)
# ============================================================
doc.add_heading('3. Chức năng sản phẩm', level=1)

doc.add_heading('3.1. Chức năng hiện tại (Vòng 1) ✅', level=2)

features = [
    ('Trưng bày Bonsai 3D & AR', 'Người bán tải lên mô hình 3D, người mua xoay 360°, phóng to/thu nhỏ. AR cho phép đặt cây ảo vào không gian thực.'),
    ('Hộ chiếu thực vật số (Digital Plant Passport)', 'Mỗi cây cảnh được cấp hộ chiếu số với UUID duy nhất. Lưu trữ timeline: quá trình tạo dáng, lịch sử sang tên, giải thưởng.'),
    ('Diễn đàn Cộng đồng', 'Thảo luận chuyên sâu: Kỹ thuật Bonsai, Khoe cây, Hỏi đáp bệnh lý. Đăng bài đa phương tiện, bình luận, upvote.'),
    ('Chat real-time với người bán', 'Nhắn tin trực tiếp Buyer ↔ Seller. Hỗ trợ văn bản, hình ảnh, chia sẻ thẻ sản phẩm. Mỗi đơn hàng có thread riêng.'),
    ('Thư viện Tri thức (Journal)', 'Kho tài liệu 20+ bài viết về 8 loài bonsai phổ biến và 11 bài hướng dẫn cơ bản.'),
    ('Đặt hàng & Vận chuyển tự động', 'Cart → Checkout với selector Tỉnh → Huyện → Xã. Phí GHTK tính real-time. Đơn hàng quản lý theo timeline.'),
    ('Sàn giao dịch đa người bán', 'Nhiều nhà vườn đăng ký cửa hàng, quản lý sản phẩm, đơn hàng, chat với khách.'),
    ('Vườn cá nhân (My Garden)', 'Tạo vườn ảo, chia vùng, gắn cây, ghi chú theo dõi sự phát triển.'),
    ('Tìm mẫu cây phù hợp (Quiz)', 'Quiz nhiều bước gợi ý loại cây phù hợp với điều kiện sống.'),
    ('Chẩn đoán bệnh cây (AI)', 'Tải ảnh cây, AI phân tích hình ảnh và đưa ra chẩn đoán bệnh lý sơ bộ.'),
    ('So sánh sản phẩm', 'Đặt nhiều sản phẩm lên bảng so sánh side-by-side.'),
    ('Affiliate (Tiếp thị liên kết)', 'Đăng ký làm affiliate, nhận referral link, dashboard theo dõi hoa hồng.'),
    ('Thông báo real-time', 'Thông báo về trạng thái đơn hàng, bình luận, tin nhắn chat.'),
    ('Xác thực 2FA', 'TOTP (Google Authenticator) và recovery codes.'),
    ('Theo dõi vận chuyển', 'Trạng thái đơn hàng real-time, mã vận đơn, estimated delivery.'),
    ('Hợp đồng điện tử (E-Contract)', 'Tạo hợp đồng mua bán, ký số, theo dõi trạng thái.'),
    ('Hoàn tiền (Refund)', 'Buyer yêu cầu hoàn tiền với lý do. Seller/Admin xử lý.'),
    ('Đánh giá sản phẩm (Reviews)', 'Buyer đánh giá sau khi nhận hàng.'),
    ('Banner & SEO', 'Banner quảng cáo trang chủ. Tự động tối ưu meta tags.'),
]

for title, desc in features:
    doc.add_heading(f'3.1.{features.index((title, desc))+1}. {title}', level=3)
    doc.add_paragraph(desc)

doc.add_heading('3.2. Chức năng phát triển (Vòng 2) 🔄', level=2)
v2 = [
    ('Trợ lý AI chuyên gia', 'Chatbot AI trả lời câu hỏi chăm sóc cây. Tự động routing tới Chuyên gia khi cần.'),
    ('Sàn đấu giá trực tuyến', 'Đấu giá time real-time cho siêu phẩm bonsai. Extensions tự động.'),
    ('Mô phỏng cắt tỉa 3D', 'Cắt tỉa thử nghiệm trên mô hình 3D trước khi thực hiện ngoài đời thực.'),
    ('Thanh toán trực tuyến', 'Tích hợp VNPay, MOMO, ZaloPay. Cơ chế Escrow.'),
    ('Ứng dụng di động', 'Flutter native với camera LiDAR, push notification, AR.'),
    ('NFT Hộ chiếu số', 'Chuyển Hộ chiếu sang NFT trên blockchain ERC-721.'),
]
for i, (t, d) in enumerate(v2, 1):
    doc.add_heading(f'3.2.{i}. {t}', level=3)
    doc.add_paragraph(d)

doc.add_page_break()

# ============================================================
# 4. CÔNG NGHỆ ÁP DỤNG (Developer-detailed)
# ============================================================
doc.add_heading('4. Công nghệ áp dụng', level=1)

doc.add_heading('4.1. Công nghệ phần cứng', level=2)
doc.add_paragraph('Điện thoại thông minh / Máy ảnh (Photogrammetry 3D)')
doc.add_paragraph('Camera LiDAR (iPhone Pro — cho Mobile App tương lai)')

doc.add_heading('4.2. Công nghệ phần mềm', level=2)
add_table(
    ['Thành phần', 'Công nghệ', 'Phiên bản', 'Ghi chú'],
    [
        ['Core Backend', 'Laravel (PHP)', '12.x (PHP 8.2+)', 'Modular Monolith architecture'],
        ['ORM', 'Eloquent', 'Laravel built-in', '60+ models, eager loading'],
        ['Real-time Engine', 'JakartaEE WebSocket', 'WildFly 40.0 (Java 21)', 'Chat server riêng biệt'],
        ['Data Protocol', 'Google Protocol Buffers', 'Latest', 'Serialize tin nhắn chat'],
        ['Frontend Framework', 'Vue.js', '3.x (Composition API)', 'Single File Components'],
        ['SPA Bridge', 'Inertia.js', 'Latest', 'Server-driven SPA routing'],
        ['CSS Framework', 'Tailwind CSS', '4.x', 'Utility-first CSS'],
        ['UI Components', 'PrimeVue', 'Latest', 'Toast, ConfirmDialog, etc.'],
        ['JS Routes', 'Ziggy.js', 'Latest', 'Named routes trong JS'],
        ['3D Graphics', 'Three.js + TresJS', 'Latest', 'WebGL rendering'],
        ['3D Viewer', 'Google Model Viewer', '<model-viewer>', 'AR/VR ready'],
        ['Compression', 'Google Draco', 'Latest', '3D mesh compression'],
        ['Queue Worker', 'Laravel Queue', 'Redis driver', 'Async jobs'],
        ['Task Scheduler', 'Laravel Scheduler', 'Cron-based', 'Scheduled tasks'],
        ['Email', 'Laravel Mail', 'Mailable', 'Transaction emails'],
    ]
)

doc.add_heading('4.3. Công nghệ tích hợp & Hệ hạ tầng', level=2)
add_table(
    ['Thành phần', 'Chi tiết', 'Mục đích'],
    [
        ['Database', 'MySQL 8.x', 'Lưu trữ dữ liệu chính (products, orders, users, address...)'],
        ['Cache', 'Redis 7.x', 'Cache sản phẩm, danh mục, session, queue'],
        ['Pub/Sub', 'Redis 7.x', 'Broadcast events (order status, forum, chat)'],
        ['Vận chuyển', 'API GHTK', 'Tính phí real-time theo mã tỉnh→huyện→xã'],
        ['AI Engine', 'Gemini Pro Vision', 'Chẩn đoán bệnh lý thực vật từ hình ảnh'],
        ['Container', 'Docker + Docker Compose', '7 containers: nginx, php, queue, scheduler, mysql, redis, wildfly'],
        ['Web Server', 'Nginx', 'Reverse proxy, static files, SSL termination'],
        ['WebSocket', 'JakartaEE WildFly 40.0', 'Chat server xử lý real-time'],
        ['CI/CD', 'GitHub Actions', 'Auto-deploy khi push lên main'],
        ['SSL', 'Cloudflare Flexible SSL', 'Origin listen port 80'],
        ['Domain', 'agriverse.slink.id.vn', 'Production deployment'],
    ]
)

doc.add_heading('4.4. Ưu điểm công nghệ', level=2)

doc.add_heading('Hiệu năng & UX', level=3)
doc.add_paragraph(
    'Vue 3 + Inertia.js giúp SPA-like experience mà không cần frontend build pipeline riêng. '
    'Draco Compression cho 3D trên di động (3G/400ms load). PrimeVue UI components.',
)

doc.add_heading('Khả năng mở rộng', level=3)
doc.add_paragraph(
    'Modular Monolith — 60+ Models, 25+ Services, 20+ Controllers. '
    'Tách module Shop, Forum, 3D, Diagnostic không ảnh hưởng nhau.',
)

doc.add_heading('Tương tác tức thì', level=3)
doc.add_paragraph(
    'JakartaEE WebSocket cho chat. Laravel Events + Redis Broadcast cho order/forum. '
    'Protobuf serialize tin nhắn chat giảm bandwidth.',
)

doc.add_heading('Bảo mật', level=3)
doc.add_paragraph(
    '2FA (TOTP), CSRF protection, Rate limiting, Session-based auth, '
    'Authorization Policies cho từng resource (OrderPolicy, ProductPolicy...).'
)

doc.add_heading('DevOps', level=3)
doc.add_paragraph(
    'Docker Compose 7 containers (nginx, php, queue, scheduler, mysql, redis, wildfly). '
    'GitHub Actions CI/CD auto-deploy. Cloudflare SSL.'
)

doc.add_page_break()

# ============================================================
# 5. KIẾN TRÚC HỆ THỐNG (Developer-detailed)
# ============================================================
doc.add_heading('5. Kiến trúc hệ thống', level=1)

doc.add_heading('5.1. Kiến trúc tổng quan — Modular Monolith', level=2)
doc.add_paragraph(
    'Hệ thống采用 Modular Monolith architecture. AgriVerse là module chính trong Laravel application:\n'
    '├── Console/          Artisan commands (fetch-bonsai-articles, seed data)\n'
    '├── Database/         Migrations (ghn_provinces, ghn_districts, ghn_wards, user_addresses)\n'
    '├── Events/           OrderCreated, ForumPostCreated...\n'
    '├── Exceptions/       InsufficientStockException\n'
    '├── Http/Controllers/ 20+ Shop controllers, Seller controllers, Admin controllers\n'
    '├── Jobs/             Queue jobs\n'
    '├── Listeners/        Event listeners\n'
    '├── Models/           60+ Eloquent models\n'
    '├── Notifications/    Email templates\n'
    '├── Policies/         Authorization policies\n'
    '├── Protobuf/         Protocol Buffer definitions (.proto files)\n'
    '├── Providers/        Service providers\n'
    '├── Routes/           shop.php, admin.php, api.php\n'
    '├── Services/         25+ business logic services\n'
    '└── Resources/js/     Vue 3 frontend (Layouts, Pages, Components, Composables)'
)

doc.add_heading('5.2. Luồng dữ liệu chi tiết', level=2)

doc.add_heading('Luồng 1: Truy cập sản phẩm', level=3)
doc.add_paragraph(
    'Browser → Nginx → Laravel (Inertia) → Eloquent → MySQL\n'
    '                         → Vue 3 render (SSR/CSR)\n'
    '                         → 3D Asset → Model Viewer (WebGL + Draco decompression)'
)

doc.add_heading('Luồng 2: Chat real-time', level=3)
doc.add_paragraph(
    'Buyer/Seller → WebSocket (JakartaEE/WildFly)\n'
    '            → Protobuf serialize → Redis Pub/Sub\n'
    '            → WebSocket broadcast → Receiver\n\n'
    'Flow chi tiết:\n'
    '1. Client gửi tin nhắn qua WebSocket connection (đã authenticate)\n'
    '2. JakartaEE endpoint nhận message, deserialize từ Protobuf\n'
    '3. Lưu tin nhắn vào MySQL (ChatMessage model)\n'
    '4. Publish lên Redis channel cho conversation group\n'
    '5. Tất cả clients trong group nhận tin nhắn real-time'
)

doc.add_heading('Luồng 3: Đặt hàng & Vận chuyển', level=3)
doc.add_paragraph(
    'Checkout Form → Laravel Controller → Validate\n'
    '             → Create Order (DB transaction + lockForUpdate)\n'
    '             → GHTK API → Calculate shipping fee\n'
    '             → OrderCreated Event → Listener (notifications)\n'
    '             → Redirect to Success page\n\n'
    'Flow chi tiết:\n'
    '1. Validate: shipping_address (required), shipping_fee (numeric), new_address (nullable array)\n'
    '2. Nếu có new_address: save UserAddress với province/district/ward text + ID\n'
    '3. DB Transaction:\n'
    '   a. lockForUpdate() Product để tránh race condition\n'
    '   b. Kiểm tra stock >= quantity\n'
    '   c. Decrement stock, tạo Order + OrderStatus\n'
    '   d. Tạo DigitalPassportLog\n'
    '   e. Xóa Cart items\n'
    '4. Dispatch OrderCreated event → gửi notifications\n'
    '5. Redirect → /agriverse/thanh-toan/thanh-cong/{order}'
)

doc.add_heading('Luồng 4: Chẩn đoán bệnh cây (AI)', level=3)
doc.add_paragraph(
    'Upload ảnh → AIPlantDoctorService → Gemini Pro Vision API\n'
    '           → Phân tích hình ảnh\n'
    '           → Trả về diagnosis + suggestions\n'
    '           → Lưu PlantDiagnosis (DB)\n'
    '           → Hiển thị kết quả cho user\n\n'
    'Service: AIPlantDoctorService.php\n'
    'Model: PlantDiagnosis, DiagnosticSymptom\n'
    'Trang: Diagnostic/Index.vue'
)

doc.add_heading('Luồng 5: Upload mô hình 3D', level=3)
doc.add_paragraph(
    'Seller upload .glb/.gltf → File storage (local)\n'
    '                        → Product.model_3d_url = path\n'
    '                        → Buyer load via <model-viewer>\n'
    '                        → WebGL render + Draco decompression\n\n'
    'Lưu ý: File size limit cần config trong PHP (upload_max_filesize, post_max_size)'
)

doc.add_heading('5.3. Container Architecture (Docker)', level=2)
doc.add_paragraph(
    'docker-compose.yml gồm 7 containers:\n'
    '├── nginx        Reverse proxy, SSL, static files\n'
    '│   └── Config: docker/nginx/conf.d/\n'
    '├── php          Laravel application (FPM)\n'
    '│   └── Config: docker/php/conf.d/sys_temp.ini\n'
    '│   └── Mount: storage/, bootstrap/cache/\n'
    '├── queue        Laravel queue worker (Redis driver)\n'
    '├── scheduler    Laravel task scheduler (cron)\n'
    '├── mysql        MySQL 8.x database\n'
    '│   └── Volume: mysql_data (persistent)\n'
    '├── redis        Redis 7.x (cache + session + pub/sub)\n'
    '└── wildfly      JakartaEE WebSocket (WildFly 40.0, Java 21)\n'
    '    └── Binary: /opt/jboss/wildfly/\n\n'
    'Lưu ý triển khai:\n'
    '• Nginx: resolver 127.0.0.11 cho runtime container DNS resolution\n'
    '• PHP: mount sys_temp_dir để tránh timeout build npm\n'
    '• WildFly: cần USER root khi build, USER jboss khi chạy\n'
    '• Redis: chạy trên port 6379 nội bộ Docker network\n'
    '• Cloudflare Flexible SSL — origin listen port 80, KHÔNG cần cert SSL trên server'
)

doc.add_heading('5.4. Bảo mật & Hiệu suất chi tiết', level=2)

doc.add_heading('Bảo mật', level=3)
doc.add_paragraph(
    '• CSRF protection trên mọi form (trừ Forum, CKFinder)\n'
    '• Session-based authentication + encrypted cookies\n'
    '• Authorization Policies cho từng resource: OrderPolicy (buyer_only, seller_only), ProductPolicy\n'
    '• Rate limiting cho API endpoints (api.rate_limit middleware)\n'
    '• 2FA (TOTP) cho tài khoản seller/admin\n'
    '• Input validation ở cả frontend (Form Request) và backend\n'
    '• File upload validation: extension whitelist, size limit\n'
    '• XSS prevention qua Blade template escaping'
)

doc.add_heading('Hiệu suất', level=3)
doc.add_paragraph(
    '• Redis cache cho sản phẩm (product cache), danh mục (category cache), session\n'
    '• Eager loading (.with()) chống N+1 query problem\n'
    '• ImageOptimizationService resize/compress ảnh khi upload\n'
    '• Vite code splitting cho frontend bundles (load trang theo route)\n'
    '• DB transaction + lockForUpdate cho order placement (tránh race condition)\n'
    '• Lazy loading cho ảnh sản phẩm trong listing\n'
    '• Pagination cho tất cả listing pages\n'
    '• Redis queue cho email sending, notification dispatch'
)

doc.add_page_break()

# ============================================================
# 6. PHƯƠNG HƯỚNG PHÁT TRIỂN
# ============================================================
doc.add_heading('6. Phương hướng phát triển', level=1)

doc.add_heading('6.1. Giai đoạn 1 (Vòng 1) ✅ Hoàn thành', level=2)
done = [
    'Core TMĐT: Đăng sản phẩm, 3D viewer, Hộ chiếu số',
    'Vận chuyển GHTK với mã tỉnh → huyện → xã (63 tỉnh, 696 huyện, 10,051 xã)',
    'Diễn đàn (Forum) với categories, upvote, comments, 20+ bài viết',
    'Chat real-time WebSocket (JakartaEE WildFly)',
    'Checkout flow với address selector Tỉnh→Huyện→Xã',
    'Multi-vendor Marketplace + Seller dashboard',
    'AI Plant Doctor (Gemini Pro Vision)',
    'Vườn cá nhân, Quiz, Compare',
    'Affiliate, 2FA, Notifications',
    'Hợp đồng điện tử, Hoàn tiền, Reviews, Coupon',
    'Docker deployment (7 containers) + CI/CD (GitHub Actions)',
]
for item in done:
    doc.add_paragraph(item, style='List Bullet')

doc.add_heading('6.2. Giai đoạn 2 (Vòng 2) 🔄 Đang phát triển', level=2)
v2_dev = [
    'Payment Gateway online (VNPay, MOMO, ZaloPay) + Escrow',
    'Nâng cấp AI chatbot chuyên gia (RAG + Vector DB)',
    'Dashboard Analytics cho Admin/Seller',
    'Push Notification hệ thống (FCM/APNs)',
    'Sàn đấu giá real-time (JakartaEE WebSocket + Redis sorted sets)',
    'Mô phỏng cắt tỉa 3D (Three.js mesh manipulation)',
]
for item in v2_dev:
    doc.add_paragraph(item, style='List Bullet')

doc.add_heading('6.3. Giai đoạn 3 (Tương lai)', level=2)
v3 = [
    'Mobile App (Flutter) với camera LiDAR cho quét 3D',
    'NFT Hộ chiếu số (ERC-721 trên Ethereum/Polygon)',
    'Multi-language support (i18n) — tiếng Việt, tiếng Anh',
    'IoT sensor theo dõi môi trường cho My Garden',
]
for item in v3:
    doc.add_paragraph(item, style='List Bullet')

doc.add_page_break()

# ============================================================
# 7. CÔNG CỤ HỖ TRỢ (Developer-detailed)
# ============================================================
doc.add_heading('7. Công cụ hỗ trợ', level=1)
add_table(
    ['Loại', 'Công cụ', 'Phiên bản', 'Mục đích'],
    [
        ['Version Control', 'Git + GitHub', 'Latest', 'Quản lý mã nguồn, code review, CI/CD'],
        ['Project Management', 'Trello', 'Web', 'Sprint planning, task tracking'],
        ['Dev IDE', 'PHP Storm / VS Code', 'Latest', 'Backend + Frontend development'],
        ['Design UI/UX', 'Figma, Stitch', 'Latest', 'Wireframe, prototype, design system'],
        ['API Testing', 'Postman', 'Latest', 'REST API testing + collections'],
        ['E2E Testing', 'Playwright', 'Latest', 'End-to-end browser testing'],
        ['Unit Testing', 'Pest PHP', 'Latest', 'PHPUnit-based testing framework'],
        ['Container', 'Docker + Docker Compose', 'Latest', 'Local dev + production deployment'],
        ['CI/CD', 'GitHub Actions', 'Latest', 'Auto-deploy on push to main'],
        ['Database GUI', 'phpMyAdmin', 'Latest', 'Database management'],
        ['3D Modeling', 'Blender + Photogrammetry', 'Latest', 'Tạo mô hình 3D từ ảnh thật'],
        ['AI', 'Google AI Studio (Gemini)', 'Pro Vision', 'API chẩn đoán bệnh cây'],
        ['WebSocket IDE', 'WildFly Management', '40.0', 'Quản lý JakartaEE server'],
    ]
)

doc.add_page_break()

# ============================================================
# 8. MÔ TẢ GIAO DIỆN & DEMO (Customer + Developer)
# ============================================================
doc.add_heading('8. Mô tả giao diện & Demo', level=1)

doc.add_heading('8.1. Triết lý thiết kế', level=2)
doc.add_paragraph(
    'Phong cách "Botanical Heritage" — tối giản, sang trọng, cảm hứng bonsai Nhật Bản.\n'
    'Typography: Roboto cho body, font display cho tiêu đề.\n\n'
    'Bảng màu:\n'
    '• Sage Green (#486730) — màu chính, đại diện thiên nhiên\n'
    '• Terracotta (#8b4f27) — màu phụ, đại diện đất nung\n'
    '• Sand (#f4f1ea) — nền nhẹ nhàng\n'
    '• White (#FCF9F8) — nền chính'
)

doc.add_heading('8.2. Các trang chính', level=2)
pages = [
    'Trang chủ: Hero ảnh bonsai, chỉ số AQI không khí, grid sản phẩm nổi bật, cửa hàng, bài viết',
    'Chi tiết SP: 3D viewer沉浸式, thông số kỹ thuật, đánh giá, sản phẩm liên quan, chat với seller',
    'Checkout: Địa chỉ Tỉnh→Huyện→Xã, phí GHTK real-time, xác nhận đơn',
    'Diễn đàn: Categories, bài viết, bình luận, upvote, real-time',
    'Seller Dashboard: Thống kê doanh thu, quản lý đơn hàng, sản phẩm, reviews',
    'Hệ thống quản trị (Admin): Quản lý users, products, orders, reports',
]
for p in pages:
    doc.add_paragraph(p, style='List Bullet')

doc.add_heading('8.3. Demo sản phẩm', level=2)
doc.add_paragraph('GitHub: https://github.com/minhdev-ops/Orchestrix')
doc.add_paragraph('Demo online: agriverse.slink.id.vn')
doc.add_paragraph(
    'Hướng dẫn cài đặt:\n'
    '1. Clone repo: git clone https://github.com/minhdev-ops/Orchestrix.git\n'
    '2. Backend: composer install, cp .env, cấu hình database, php artisan migrate --seed\n'
    '3. Frontend: npm install && npm run build\n'
    '4. Docker: docker-compose up -d\n'
    '5. Seed địa chỉ: php artisan db:seed --class=GhnAddressSeeder\n'
    '6. Đăng nhập với tài khoản Admin/Seller/Buyer mẫu'
)

doc.add_page_break()

# ============================================================
# 9. DANH SÁCH TÍNH NĂNG ĐẦY ĐỦ (Developer)
# ============================================================
doc.add_heading('9. Danh sách tính năng đầy đủ', level=1)
add_table(
    ['Module', 'Mô tả', 'Backend Models', 'Frontend Pages', 'Trạng thái'],
    [
        ['Shop - Products', 'Đăng/bán SP, variants, ảnh, 3D', 'Product, ProductVariant, ProductImage, ThreeDAsset', 'Products/Index, Show', '✅'],
        ['Shop - Stores', 'Cửa hàng, verification', 'Store, SellerVerification, StoreSubscription', 'Stores/Index, Show', '✅'],
        ['Shop - Cart/Checkout', 'Giỏ hàng, Checkout, GHTK', 'Cart, UserAddress, Province, District, Ward', 'Cart/Index, Checkout/Index, Success', '✅'],
        ['Shop - Orders', 'Quản lý đơn, timeline', 'Order, OrderStatus, DigitalPassportLog', 'Orders/Index, Show', '✅'],
        ['Shop - Chat', 'Chat real-time WebSocket', 'ChatMessage, ChatConversation, ChatGroup', 'Components/ChatBox, ChatPanel', '✅'],
        ['Shop - Forum', 'Diễn đàn cộng đồng', 'ForumPost, ForumComment, ForumLike, ForumCategory', 'Forum/Index, Show, Create', '✅'],
        ['Shop - Journal', 'Thư viện bài viết', 'JournalArticle', 'Journal/Index, Show', '✅'],
        ['Shop - Garden', 'Vườn cá nhân', 'Garden, GardenPlant, GardenZone', 'Garden/Index', '✅'],
        ['Shop - Quiz', 'Tìm mẫu cây', 'QuizQuestion', 'Quiz/Index', '✅'],
        ['Shop - Diagnostic', 'Chẩn đoán bệnh (AI)', 'PlantDiagnosis, DiagnosticSymptom', 'Diagnostic/Index', '✅'],
        ['Shop - Sustainability', 'Báo cáo bền vững', 'SustainabilityReport', 'Sustainability/Index', '✅'],
        ['Shop - AR', 'Trình xem AR', '—', 'AR/Index, Components/ARViewer', '✅'],
        ['Shop - Compare', 'So sánh SP', '—', 'Compare/Index, CompareBar', '✅'],
        ['Shop - Contract', 'Hợp đồng điện tử', 'Contract', 'Contracts/Show', '✅'],
        ['Shop - Affiliate', 'Tiếp thị liên kết', 'Affiliate, Referral, Commission', 'Affiliate/Register, Dashboard', '✅'],
        ['Shop - Tracking', 'Theo dõi vận chuyển', '—', 'Tracking/Index', '✅'],
        ['Seller', 'Dashboard, quản lý đơn/sp', 'Order, Product, Store', 'Seller/*', '✅'],
        ['Admin', 'Quản trị hệ thống', 'All models', 'Admin/*', '✅'],
        ['Auth', 'Đăng nhập, 2FA, quên MK', 'User, TwoFactorService', 'Settings/TwoFactor, Login, Register', '✅'],
        ['Payment (Online)', 'Cổng thanh toán online', 'Transaction, PaymentService', 'Payment/Index, Banking', '🔄'],
        ['Auction', 'Đấu giá real-time', '—', '—', '❌'],
        ['Virtual Pruning', 'Mô phỏng cắt tỉa 3D', '—', '—', '❌'],
        ['NFT Passport', 'Blockchain NFT', '—', '—', '❌'],
        ['Mobile App', 'Flutter', '—', '—', '❌'],
    ]
)

doc.add_page_break()

# ============================================================
# 10. TÀI LIỆU THAM KHẢO
# ============================================================
doc.add_heading('10. Tài liệu tham khảo', level=1)
refs = [
    'Laravel 12.x: https://laravel.com/docs/12.x',
    'Vue.js 3 + Inertia.js: https://vuejs.org, https://inertiajs.com',
    'Google Model Viewer: https://modelviewer.dev',
    'Three.js: https://threejs.org',
    'Laravel Reverb: https://reverb.laravel.com',
    'JakartaEE WebSocket: https://jakarta.ee/specifications/websocket/',
    'Protocol Buffers: https://protobuf.dev',
    'GHTK API: https://docs.giaohangtietkiem.vn',
    'PrimeVue: https://primevue.org',
    'Ziggy.js: https://ziggy.dev',
    'Gemini API: https://ai.google.dev',
    'Tailwind CSS: https://tailwindcss.com',
    'Docker: https://docs.docker.com',
    'GitHub Actions: https://docs.github.com/en/actions',
    'Tài liệu chuyên ngành: Kỹ thuật chăm sóc, định hình bonsai',
]
for r in refs:
    doc.add_paragraph(r, style='List Number')

doc.add_paragraph()
doc.add_page_break()

# ============================================================
# 11. LỜI CẢM ƠN
# ============================================================
doc.add_heading('11. Lời cảm ơn', level=1)
doc.add_paragraph(
    'Em xin gửi lời cảm ơn đến quý Thầy Cô trong khoa Công nghệ Thông tin, '
    'trường Đại học Nông Lâm TP. Hồ Chí Minh đã truyền đạt cho em những nền tảng '
    'kiến thức vững chắc trong suốt những năm học vừa qua.'
)
doc.add_paragraph(
    'Cuối cùng, em xin gửi lời tri ân đến gia đình và bạn bè đã luôn động viên, '
    'hỗ trợ và tạo mọi điều kiện tốt nhất để em hoàn thành tốt đề tài này. '
    'Mặc dù đã rất cố gắng, nhưng do hạn chế về mặt thời gian và kinh nghiệm thực tế, '
    'đề tài chắc chắn không tránh khỏi những thiếu sót. Em rất mong nhận được sự góp ý '
    'và chỉ bảo thêm từ hội đồng đánh giá để hệ thống có thể hoàn thiện hơn trong tương lai.'
)
doc.add_paragraph('Xin trân trọng cảm ơn!')

doc.add_paragraph()
doc.add_paragraph()
add_centered('TP. Hồ Chí Minh, ngày     tháng     năm 2026', 12, False)
doc.add_paragraph()
add_centered('Tác giả hoặc đại diện nhóm tác giả', 12, True)
add_centered('(Ký, ghi rõ họ tên)', 12, False)

# Save
output_path = '/home/wanmin/1.Work/03.Project/2026/Orchestrix/app/Modules/AgriVerse/document/IT_Challenge_Bang_B_Full.docx'
doc.save(output_path)
print(f'Saved: {output_path}')
