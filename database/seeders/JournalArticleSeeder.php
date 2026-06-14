<?php

namespace Database\Seeders;

use App\Modules\AgriVerse\Models\JournalArticle;
use Illuminate\Database\Seeder;

class JournalArticleSeeder extends Seeder
{
    public function run(): void
    {
        JournalArticle::create([
            'title' => 'Di sản Di truyền của Monstera Albo',
            'slug' => 'di-san-di-truyen-monstera-albo',
            'tag' => 'Tạp chí Nghiên cứu Tập. 14 | Di truyền học',
            'hero_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCntKtchAAxiNYDh0YPHZw4szDqmGM5efH-o78N50Awx_hINxciPZHAy1GA3hkxwkQvNJkMYUALnWk0OrhZHUQjx8ol-qJOKgqN2tbniy5cxVuH5P6OIQ2akqe0uI6IOUN-alAzR0NxbF0Qmk6DBrwoZxLzoEe-9kaurwWRNNJkO-035ZGafrE0N4b1aFFhJFqmON1nXf8diYkjhsibc0Nk7G7R87QX85avzKYhOPW2ym1swa94bg1_IZOP36cA10D-3CNwXvwJXJw',
            'author_name' => 'Tiến sĩ Elena Vance',
            'author_role' => 'Khoa Thực vật học Phân tử',
            'author_institution' => 'Viện Lumina',
            'abstract' => '<i>Monstera deliciosa</i> \'Albo Borsigiana\' là đỉnh cao của đột biến soma tự phát trong họ Ráy. Bài báo này khám phá bản chất khảm không ổn định của sự biến màu, tập trung vào các lớp mô phân sinh L1 và L2 cùng các kích hoạt biểu sinh dẫn đến sự thoái hóa hoặc biến màu cực đoan. Bằng cách phân tích độ ổn định của đột biến trên 500 mẫu vật được nuôi trồng, chúng tôi thiết lập một khuôn khổ mới để dự đoán kết quả kiểu hình ở các tài sản thực vật có giá trị cao.',
            'content' => 'Các quan sát được thực hiện bằng kính hiển vi điện tử và theo dõi tăng trưởng dọc. Chúng tôi giả thuyết rằng sự biến màu không hoàn toàn ngẫu nhiên mà tuân theo mô hình lá xoắn ốc chịu ảnh hưởng từ tính toàn vẹn cấu trúc của mô phân sinh ngọn. Đột biến "Albo" khác biệt đáng kể so với đột biến "Thai Constellation", vì đột biến trước là soma trong khi đột biến sau là sự thay đổi thể đa bội ổn định do phòng thí nghiệm tạo ra.',
            'blockquote_text' => '"Sự bất ổn định di truyền của mẫu Albo không phải là khiếm khuyết của tự nhiên, mà là minh chứng cho khả năng thích ứng năng động của tế bào thực vật dưới các tác nhân gây căng thẳng môi trường."',
            'blockquote_author' => 'Tiến sĩ Vance',
            'doi' => '10.1038/bot.2024.11',
            'is_peer_reviewed' => true,
            'read_time_minutes' => 14,
            'citation_count' => 24,
            'citations' => json_encode([
                'Araceae Genome Project (2022). <i>Phylogenetic analysis of chimeric mutations in tropical epiphytes.</i> Oxford Botany Journal.',
                'Matsumoto, K. (2019). <i>Somatic cell inheritance in variegated Monstera cultivars.</i> Kyoto Agricultural Review.',
                'Lumina Tech Reports (2023). <i>Chlorophyll-depleted growth patterns in high-UV environments.</i>',
            ]),
            'related_specimens' => json_encode([
                ['name' => 'Monstera Thai Constellation', 'description' => 'Stable mutation via ploidy change', 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAQe3hZWiBzIMTUyRRwO3RMmTYoY0I66SU-_jisXIObtKacyZMnaykAwlDuCXtEqcswKkeshrKSW_cfQHp16A3TTP7KViwAeE9Wectdvij__wBNEzLP28hn6F5zvUNptDAWgd8mqv4RRdyxxnONDwo7XFBGdw0pchDMRCsWptBW2bEtpmx_TrZNmwOUa_dkKafhfdWGMRp1P7E8Oez7LbYjuxKgnTlp6vWAEnCyPdXxtF1qolVK3TRY8HbQ5z3H2OPHqSaje3kJZ2I'],
                ['name' => 'Philodendron Pink Princess', 'description' => 'L1-dominant anthocyanin mutation', 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAx3c1j4cunOhsx7RbaKg2Ux59YtDb4-bTYYRqhezDxWkNtB_FZMsVVHkxKLXj9gtbPbFOlU50BMbr2VCHc7a5XsQ5-uPeG6X8yW7oCYFcvxdDOTI7bJjAj0YuGgtvWaxbdX_TE6P4pIeuv7seCdAVPpCfHMBkqSFq0wFyORau_PEp230rwALqRvM1NmkTXs7hW1Liupe5ClgfXE_VmsC-LmtFBGmScJRG2O68NKFigscH5XAT2NHXVKO9ONVVzuXL3c9TMhCf8Jvg'],
            ]),
            'published_at' => now(),
        ]);
    }
}
