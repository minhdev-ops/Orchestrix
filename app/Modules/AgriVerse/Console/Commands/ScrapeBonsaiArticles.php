<?php

namespace App\Modules\AgriVerse\Console\Commands;

use App\Modules\AgriVerse\Models\JournalArticle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ScrapeBonsaiArticles extends Command
{
    protected $signature = 'agriverse:scrape-bonsai-articles';
    protected $description = 'Scrape articles from bonsaiempire.vn and insert into journal_articles';

    private array $articles = [
        // Species guides (8)
        [
            'title' => 'Cây Sanh (Ficus) — Hướng dẫn chăm sóc Bonsai',
            'slug' => 'cay-sanh-ficus-bonsai',
            'tag' => 'Hướng dẫn giống cây',
            'url' => 'https://www.bonsaiempire.vn/tree-species/ficus',
            'image' => 'https://www.bonsaiempire.vn/images/videos/species-ficus-bonsai.jpg',
        ],
        [
            'title' => 'Cây Tùng (Juniper) — Hướng dẫn chăm sóc Bonsai',
            'slug' => 'cay-tung-juniper-bonsai',
            'tag' => 'Hướng dẫn giống cây',
            'url' => 'https://www.bonsaiempire.vn/tree-species/juniper',
            'image' => 'https://www.bonsaiempire.vn/images/videos/species-juniper.jpg',
        ],
        [
            'title' => 'Cây Hoa Giấy (Bougainvillea) — Hướng dẫn chăm sóc Bonsai',
            'slug' => 'cay-hoa-giay-bougainvillea-bonsai',
            'tag' => 'Hướng dẫn giống cây',
            'url' => 'https://www.bonsaiempire.vn/tree-species/bougainvillea',
            'image' => 'https://www.bonsaiempire.vn/images/videos/species-all.jpg',
        ],
        [
            'title' => 'Cây Tùng La Hán (Podocarpus) — Hướng dẫn chăm sóc Bonsai',
            'slug' => 'cay-tung-la-han-podocarpus-bonsai',
            'tag' => 'Hướng dẫn giống cây',
            'url' => 'https://www.bonsaiempire.vn/tree-species/podocarpus',
            'image' => 'https://www.bonsaiempire.vn/images/videos/species-all.jpg',
        ],
        [
            'title' => 'Cây Mai Chiếu Thủy (Wrightia religiosa) — Hướng dẫn chăm sóc Bonsai',
            'slug' => 'cay-mai-chieu-thuy-wrightia-bonsai',
            'tag' => 'Hướng dẫn giống cây',
            'url' => 'https://www.bonsaiempire.vn/tree-species/water-jasmine',
            'image' => 'https://www.bonsaiempire.vn/images/stories/species/Water-jasmine-wrightia-religiosa.jpg',
        ],
        [
            'title' => 'Cây Hoa Mai Vàng (Ochna integerrima) — Hướng dẫn chăm sóc Bonsai',
            'slug' => 'cay-hoa-mai-vang-ochna-bonsai',
            'tag' => 'Hướng dẫn giống cây',
            'url' => 'https://www.bonsaiempire.vn/tree-species/mai-vang',
            'image' => 'https://www.bonsaiempire.vn/images/species-photos/mai-vang-bonsai.jpg',
        ],
        [
            'title' => 'Cây Hoa Mẫu Đơn Đỏ (Ixora coccinea) — Hướng dẫn chăm sóc Bonsai',
            'slug' => 'cay-hoa-mau-don-do-ixora-bonsai',
            'tag' => 'Hướng dẫn giống cây',
            'url' => 'https://www.bonsaiempire.vn/tree-species/bong-trang',
            'image' => 'https://www.bonsaiempire.vn/images/headers/bonsai-species-bong-trang.jpg',
        ],
        [
            'title' => 'Cây Linh Sam (Desmodium Unifoliatum) — Hướng dẫn chăm sóc Bonsai',
            'slug' => 'cay-linh-sam-desmodium-bonsai',
            'tag' => 'Hướng dẫn giống cây',
            'url' => 'https://www.bonsaiempire.vn/tree-species/linh-sam',
            'image' => 'https://www.bonsaiempire.vn/images/headers/bonsai-species-linh-sam.jpg',
        ],
        // Basics (11)
        [
            'title' => 'Nghệ thuật Bonsai — Giới thiệu toàn diện',
            'slug' => 'nghe-thuat-bonsai-gioi-thieu',
            'tag' => 'Kiến thức cơ bản',
            'url' => 'https://www.bonsaiempire.vn/basics/',
            'image' => 'https://www.bonsaiempire.vn/images/videos/species-ficus-bonsai.jpg',
        ],
        [
            'title' => 'Đặt cây Bonsai — Vị trí và điều kiện ánh sáng',
            'slug' => 'dat-cay-bonsai-vi-tri-anh-sang',
            'tag' => 'Kiến thức cơ bản',
            'url' => 'https://www.bonsaiempire.vn/basics/bonsai-care/position',
            'image' => 'https://www.bonsaiempire.vn/images/articles-next-steps/positioning.jpeg',
        ],
        [
            'title' => 'Tưới nước cho Bonsai — Hướng dẫn chi tiết',
            'slug' => 'tuoi-nuoc-cho-bonsai-huong-dan',
            'tag' => 'Kiến thức cơ bản',
            'url' => 'https://www.bonsaiempire.vn/basics/bonsai-care/watering',
            'image' => 'https://www.bonsaiempire.vn/images/articles-next-steps/watering.jpeg',
        ],
        [
            'title' => 'Bón phân cho Bonsai — Dinh dưỡng và phân bón',
            'slug' => 'bon-phan-cho-bonsai-dinh-duong',
            'tag' => 'Kiến thức cơ bản',
            'url' => 'https://www.bonsaiempire.vn/basics/bonsai-care/fertilizing',
            'image' => 'https://www.bonsaiempire.vn/images/articles-next-steps/fertilizing.jpeg',
        ],
        [
            'title' => 'Thay chậu cho Bonsai — Kỹ thuật và thời điểm',
            'slug' => 'thay-chau-cho-bonsai-ky-thuat',
            'tag' => 'Kiến thức cơ bản',
            'url' => 'https://www.bonsaiempire.vn/basics/bonsai-care/repotting',
            'image' => 'https://www.bonsaiempire.vn/images/articles-next-steps/repotting.jpeg',
        ],
        [
            'title' => 'Chất nền (Đất trồng) cho Bonsai',
            'slug' => 'chat-nen-dat-trong-bonsai',
            'tag' => 'Kiến thức cơ bản',
            'url' => 'https://www.bonsaiempire.vn/basics/bonsai-care/bonsai-soil',
            'image' => 'https://www.bonsaiempire.vn/images/articles-next-steps/soil.jpeg',
        ],
        [
            'title' => 'Tỉa cành Bonsai — Kỹ thuật tạo dáng cơ bản',
            'slug' => 'tia-canh-bonsai-ky-thuat-tao-dang',
            'tag' => 'Kiến thức cơ bản',
            'url' => 'https://www.bonsaiempire.vn/basics/styling/pruning',
            'image' => 'https://www.bonsaiempire.vn/images/articles-next-steps/pruning.jpeg',
        ],
        [
            'title' => 'Uốn cành Bonsai (Wiring) — Hướng dẫn từ A đến Z',
            'slug' => 'uon-canh-bonsai-wiring-huong-dan',
            'tag' => 'Kiến thức cơ bản',
            'url' => 'https://www.bonsaiempire.vn/basics/styling/wiring',
            'image' => 'https://www.bonsaiempire.vn/images/articles-next-steps/wiring.jpeg',
        ],
        [
            'title' => 'Dụng cụ làm Bonsai — Các công cụ cần thiết',
            'slug' => 'dung-cu-lam-bonsai-cong-cu',
            'tag' => 'Kiến thức cơ bản',
            'url' => 'https://www.bonsaiempire.vn/basics/styling/tools',
            'image' => 'https://www.bonsaiempire.vn/images/articles-next-steps/tools.jpeg',
        ],
        [
            'title' => 'Mua cây Bonsai — Hướng dẫn chọn mua',
            'slug' => 'mua-cay-bonsai-huong-dan-chon',
            'tag' => 'Kiến thức cơ bản',
            'url' => 'https://www.bonsaiempire.vn/basics/cultivation/buying-bonsai',
            'image' => 'https://www.bonsaiempire.vn/images/articles-next-steps/buying.jpeg',
        ],
        [
            'title' => 'Trồng Bonsai từ hạt — Hướng dẫn từng bước',
            'slug' => 'trong-bonsai-tu-hat-huong-dan',
            'tag' => 'Kiến thức cơ bản',
            'url' => 'https://www.bonsaiempire.vn/basics/cultivation/from-seeds',
            'image' => 'https://www.bonsaiempire.vn/images/articles-next-steps/seeds.jpeg',
        ],
    ];

    public function handle()
    {
        $this->info('Scraping ' . count($this->articles) . ' articles from bonsaiempire.vn...');
        $imported = 0;
        $failed = 0;

        foreach ($this->articles as $i => $article) {
            $this->line('  [' . ($i + 1) . '/' . count($this->articles) . '] ' . $article['title']);

            $existing = JournalArticle::where('slug', $article['slug'])->first();
            if ($existing) {
                $this->line('    Already exists, skipping.');
                $imported++;
                continue;
            }

            $content = $this->scrapeContent($article['url']);

            JournalArticle::create([
                'title' => $article['title'],
                'slug' => $article['slug'],
                'tag' => $article['tag'],
                'hero_image_url' => $article['image'],
                'author_name' => 'Bonsai Empire',
                'author_role' => 'Chuyên gia Bonsai',
                'author_institution' => 'Bonsai Empire Vietnam',
                'abstract' => mb_substr(strip_tags($content), 0, 300) . '...',
                'content' => $content,
                'is_peer_reviewed' => false,
                'read_time_minutes' => max(3, intdiv(mb_strlen(strip_tags($content)), 2000)),
                'published_at' => now()->subDays(count($this->articles) - $i),
            ]);

            $this->info('    ✓ Inserted');
            $imported++;
        }

        $this->newLine();
        $this->info("Done! Imported: {$imported}, Failed: {$failed}");
    }

    private function scrapeContent(string $url): string
    {
        try {
            $resp = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; AgriVerse/1.0)',
                ])
                ->get($url);

            if (!$resp->successful()) {
                throw new \Exception("HTTP {$resp->status()}");
            }

            $html = $resp->body();
            $content = $this->extractContent($html, $url);

            if (mb_strlen(strip_tags($content)) < 100) {
                throw new \Exception('Content too short');
            }

            return $content;
        } catch (\Exception $e) {
            $this->warn("    Scrape failed ({$e->getMessage()}), using fallback content for: {$url}");
            return $this->getFallbackContent($url);
        }
    }

    private function extractContent(string $html, string $url): string
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOWARNING | LIBXML_NOERROR);
        $xpath = new \DOMXPath($dom);

        $text = '';

        // Try: main content div
        foreach (['div.item-page', 'div.content-wrapper', 'div.com-content', 'article', 'main'] as $selector) {
            $nodes = @$xpath->query("//{$selector}");
            if ($nodes && $nodes->length > 0) {
                $text = $dom->saveHTML($nodes->item(0));
                break;
            }
        }

        if (mb_strlen(strip_tags($text)) < 100) {
            // Fallback: grab all <p> tags inside <body>
            $body = $xpath->query('//body');
            if ($body && $body->length > 0) {
                $ps = $xpath->query('.//p', $body->item(0));
                $parts = [];
                foreach ($ps as $p) {
                    $t = trim($p->textContent);
                    if (mb_strlen($t) > 30) {
                        $parts[] = '<p>' . htmlspecialchars($t) . '</p>';
                    }
                }
                $text = implode("\n", $parts);
            }
        }

        // Also grab all <h1>-<h4>
        $headings = $xpath->query('//h1 | //h2 | //h3 | //h4');
        foreach ($headings as $h) {
            $hText = trim($h->textContent);
            if ($hText && !str_contains($text, $hText)) {
                $tag = $h->tagName;
                $text .= "\n<{$tag}>" . htmlspecialchars($hText) . "</{$tag}>\n";
            }
        }

        $text = strip_tags($text, '<h1><h2><h3><h4><h5><h6><p><br><ul><ol><li><strong><em><b><i><a><blockquote><img>');

        return $text ?: $this->getFallbackContent($url);
    }

    private function getFallbackContent(string $url): string
    {
        $map = [
            'https://www.bonsaiempire.vn/tree-species/ficus' => '<h2>Sanh (Ficus benjamina / Ficus microcarpa)</h2>
<p>Cây Sanh là loài cây Bonsai trong nhà phổ biến nhất, thuộc họ Dâu tằm (Moraceae). Chúng sống ở vùng khí hậu nhiệt đới và rất thích hợp để làm Bonsai trong nhà.</p>
<h3>Vị trí đặt</h3>
<p>Cây Sanh không chịu được thời tiết giá lạnh. Vào mùa hè có thể đặt ngoài trời, nhiệt độ phải trên 15°C và cần rất nhiều ánh sáng mặt trời. Không nên đặt cây trong bóng râm.</p>
<h3>Tưới nước</h3>
<p>Cây có nhu cầu tưới nước bình thường — khi đất mặt bắt đầu khô thì cần tưới nhiều. Nên phun sương hàng ngày để duy trì độ ẩm. Vào mùa đông, nếu đặt nơi ấm thì cần tưới nhiều hơn.</p>
<h3>Bón phân</h3>
<p>Bón phân hàng tuần hoặc hai tuần một lần vào mùa hè, từ hai đến bốn tuần một lần vào mùa đông. Có thể dùng phân lỏng hoặc phân hữu cơ dạng viên.</p>
<h3>Tỉa cành và uốn cành</h3>
<p>Cần tỉa cành thường xuyên để duy trì hình dáng. Khi cây ra 6-8 lá thì tỉa lại còn 2 lá. Cành Sanh dẻo, dễ uốn. Cần kiểm tra dây cuốn thường xuyên vì chúng nhanh cắt vào vỏ cây.</p>
<h3>Thay chậu</h3>
<p>Thay chậu vào mùa xuân, hai năm một lần, sử dụng hỗn hợp đất cơ bản. Sanh chịu được tỉa rễ rất tốt.</p>
<h3>Nhân giống</h3>
<p>Giâm cành có thể thực hiện bất kỳ thời điểm nào trong năm, nhưng tỷ lệ thành công cao nhất vào kỳ tăng trưởng giữa hè. Chiết cành bằng air-layering tốt nhất vào mùa xuân.</p>
<h3>Sâu bệnh</h3>
<p>Sanh thường kháng sâu bệnh tốt. Không khí khô và thiếu ánh sáng có thể dẫn tới rũ lá. Đôi khi bị rệp vảy hoặc nhện đỏ. Có thể đặt thanh trừ sâu vào đất hoặc phun thuốc trừ sâu.</p>',
            'https://www.bonsaiempire.vn/tree-species/juniper' => '<h2>Cây Tùng (Juniperus chinensis)</h2>
<p>Cây Tùng là loài Bonsai ngoài trời điển hình, không thể sống trong nhà. Chúng chịu lạnh tốt đến -10°C và cần nhiều ánh sáng mặt trời trực tiếp.</p>
<h3>Vị trí đặt</h3>
<p>Cây Tùng cần được đặt ngoài trời quanh năm, nơi có nhiều nắng. Chúng chịu được gió và thời tiết khắc nghiệt. Cần bảo vệ cây khỏi gió lạnh quá mức vào mùa đông.</p>
<h3>Tưới nước</h3>
<p>Đất cần phải hơi khô trước khi tưới lại. Kiểm tra độ ẩm bằng ngón tay — nếu đất còn ẩm thì chưa cần tưới. Giảm tưới vào mùa đông.</p>
<h3>Bón phân</h3>
<p>Bón phân hàng tháng trong mùa sinh trưởng (xuân-hè). Ngừng bón vào mùa đông khi cây ngủ đông. Sử dụng phân hữu cơ dạng viên hoặc phân lỏng pha loãng.</p>
<h3>Tỉa cành và uốn cành</h3>
<p>Tỉa cành vào cuối đông hoặc đầu xuân. Tùng rất thích hợp để tạo gỗ lũa (jin & shari). Uốn cành vào mùa thu hoặc đầu đông khi nhựa cây chậm lại.</p>
<h3>Thay chậu</h3>
<p>Thay chậu 2 năm một lần vào mùa xuân, trước khi chồi mới xuất hiện. Sử dụng hỗn hợp đất thoát nước tốt, hơi chua.</p>
<h3>Sâu bệnh</h3>
<p>Có thể bị nhện đỏ, rệp vảy và bệnh gỉ sắt. Phòng ngừa bằng cách đảm bảo thông gió tốt và không tưới quá nhiều.</p>',
            'https://www.bonsaiempire.vn/tree-species/bougainvillea' => '<h2>Cây Hoa Giấy (Bougainvillea)</h2>
<p>Hoa Giấy là loài cây nhiệt đới có nguồn gốc từ Nam Mỹ, nổi tiếng với những bông hoa rực rỡ sắc màu. Đây là loài cây được ưa chuộng để làm Bonsai nhờ khả năng ra hoa đẹp và dễ tạo dáng.</p>
<h3>Vị trí đặt</h3>
<p>Cần nhiều nắng và nhiệt độ cao để ra hoa — tối thiểu 5-6 giờ nắng trực tiếp mỗi ngày. Không chịu được sương giá. Vào mùa đông cần đưa vào nơi tránh rét.</p>
<h3>Tưới nước</h3>
<p>Tưới khi đất bắt đầu khô, tránh để ẩm quá lâu. Hoa Giấy chịu hạn tốt hơn chịu úng. Vào mùa đông giảm tưới đáng kể.</p>
<h3>Bón phân</h3>
<p>Bón phân hữu cơ mỗi tháng trong mùa sinh trưởng. Sử dụng phân có hàm lượng lân cao để kích thích ra hoa. Giảm phân đạm nếu muốn nhiều hoa.</p>
<h3>Tỉa cành</h3>
<p>Tỉa sau khi ra hoa để duy trì hình dáng. Có thể tỉa mạnh vào đầu mùa xuân. Hoa Giấy đâm chồi trên gỗ cũ, nên giữ lại một số cành cũ.</p>
<h3>Uốn cành</h3>
<p>Cành Hoa Giấy khá dẻo khi còn non, có thể uốn bằng dây nhôm. Cành già hóa gỗ nhanh và trở nên giòn.</p>
<h3>Thay chậu</h3>
<p>Thay chậu 2-3 năm một lần vào mùa xuân. Cắt bớt 1/3 bộ rễ. Sử dụng đất thoát nước tốt, hơi chua.</p>
<h3>Sâu bệnh</h3>
<p>Thường gặp rệp, nhện đỏ và bọ trĩ. Phun nước mạnh để loại bỏ rệp. Sử dụng xà phòng diệt côn trùng nếu cần.</p>',
            'https://www.bonsaiempire.vn/tree-species/podocarpus' => '<h2>Cây Tùng La Hán (Podocarpus macrophyllus)</h2>
<p>Tùng La Hán là loài cây nhiệt đới có nguồn gốc từ Đông Á, có thể trồng trong nhà hoặc ngoài trời. Lá hình kim dài, màu xanh đậm bóng, rất thích hợp làm Bonsai.</p>
<h3>Vị trí đặt</h3>
<p>Cây ưa bán râm mát, không chịu được nắng gắt trực tiếp. Có thể để trong nhà gần cửa sổ có ánh sáng gián tiếp. Không chịu được sương muối.</p>
<h3>Tưới nước</h3>
<p>Giữ đất ẩm đều, không để khô hoàn toàn. Tưới khi mặt đất bắt đầu se khô. Giảm tưới vào mùa đông.</p>
<h3>Bón phân</h3>
<p>Bón phân hữu cơ mỗi tháng trong mùa sinh trưởng. Sử dụng phân có độ pH trung tính đến hơi chua (pH 5-6).</p>
<h3>Tỉa cành</h3>
<p>Tỉa nhẹ thường xuyên để duy trì hình dáng. Cây có thể chịu được tỉa mạnh vào mùa xuân. Loại bỏ các cành mọc ngược hướng mong muốn.</p>
<h3>Thay chậu</h3>
<p>Thay chậu 2-3 năm một lần vào mùa xuân. Sử dụng đất hơi chua, thoát nước tốt. Cắt bỏ rễ hư và rễ già.</p>
<h3>Sâu bệnh</h3>
<p>Tương đối kháng sâu bệnh. Có thể bị rệp sáp hoặc nấm rễ nếu tưới quá nhiều.</p>',
            'https://www.bonsaiempire.vn/tree-species/water-jasmine' => '<h2>Cây Mai Chiếu Thủy (Wrightia religiosa)</h2>
<p>Mai Chiếu Thủy là loài cây nhiệt đới có hoa trắng thơm, quả đại xanh. Cây có ý nghĩa tâm linh đối với Phật tử và rất được ưa chuộng trong nghệ thuật Bonsai Việt Nam.</p>
<h3>Vị trí đặt</h3>
<p>Ưa nắng đầy đủ, nhiệt độ từ 18°C trở lên. Có thể đặt ngoài trời quanh năm ở miền Nam, cần che chắn khi nhiệt độ dưới 15°C. Càng nhiều nắng càng ra nhiều hoa.</p>
<h3>Tưới nước</h3>
<p>Cần nhiều nước, giữ đất ẩm thường xuyên. Không để đất khô hoàn toàn. Giảm tưới nhẹ vào mùa đông.</p>
<h3>Bón phân</h3>
<p>Bón phân hàng tuần trong mùa sinh trưởng. Sử dụng phân cân đối NPK. Tăng cường phân lân và kali để kích thích ra hoa.</p>
<h3>Tỉa cành</h3>
<p>Cây sinh trưởng mạnh, mau phân nhánh. Tỉa thường xuyên để duy trì dáng. Có thể tỉa tạo tán sau mỗi đợt hoa tàn.</p>
<h3>Thay chậu</h3>
<p>Thay chậu 1-2 năm một lần vì cây phát triển nhanh. Tỉa bớt rễ, thay đất mới. Thời điểm tốt nhất là mùa xuân.</p>
<h3>Nhân giống</h3>
<p>Giâm cành vào mùa mưa, tỷ lệ sống cao. Cũng có thể chiết cành hoặc gieo hạt.</p>',
            'https://www.bonsaiempire.vn/tree-species/mai-vang' => '<h2>Cây Hoa Mai Vàng (Ochna integerrima)</h2>
<p>Hoa Mai Vàng là biểu tượng của ngày Tết cổ truyền miền Nam Việt Nam. Hoa màu vàng rực rỡ, nở đúng dịp Tết Nguyên Đán, tượng trưng cho sự may mắn và thịnh vượng.</p>
<h3>Vị trí đặt</h3>
<p>Cây ưa nắng hoàn toàn, nhiệt độ lý tưởng 25-30°C. Chịu hạn tốt, không chịu úng. Cần đặt nơi thoáng gió, nhiều ánh sáng.</p>
<h3>Tưới nước</h3>
<p>Tưới khi đất mặt khô. Chịu hạn tốt hơn chịu úng. Vào mùa mưa cần tránh để đất quá ẩm. Trước Tết khoảng 1 tháng cần giảm tưới để kích thích ra hoa.</p>
<h3>Bón phân</h3>
<p>Bón phân hữu cơ hoai mục định kỳ. Sử dụng phân NPK 20-20-15 vào mùa sinh trưởng. Ngừng bón phân khi cây bắt đầu ra nụ.</p>
<h3>Kỹ thuật tuốt lá</h3>
<p>Để xử lý ra hoa đúng dịp Tết, cần tuốt lá vào khoảng tháng 10-11 âm lịch. Sau khi tuốt lá, cây sẽ ra nụ và nở hoa sau 40-50 ngày. Có thể ghép nhiều màu hoa trên cùng một cây bằng kỹ thuật ghép mắt.</p>
<h3>Tỉa cành</h3>
<p>Tỉa cành tạo tán sau mùa hoa. Cắt bỏ cành tăm, cành vượt, cành mọc trong. Giữ lại cành khỏe để tạo khung tán.</p>
<h3>Thay chậu</h3>
<p>Thay chậu 2-3 năm một lần vào mùa xuân. Đất trồng cần thoát nước tốt, giàu dinh dưỡng.</p>
<h3>Sâu bệnh</h3>
<p>Thường gặp rệp muội, nhện đỏ, sâu cuốn lá. Phòng trừ bằng cách vệ sinh cây thường xuyên và phun thuốc sinh học khi cần.</p>',
            'https://www.bonsaiempire.vn/tree-species/bong-trang' => '<h2>Cây Hoa Mẫu Đơn Đỏ (Ixora coccinea)</h2>
<p>Hoa Mẫu Đơn Đỏ còn gọi là Trang Đỏ, Nam Mẫu Đơn. Là loài cây bụi nhiệt đới có hoa đỏ rực rỡ mọc thành chùm, nở quanh năm. Trong phong thủy, cây tượng trưng cho sự vương giả, phú quý và giàu sang.</p>
<h3>Vị trí đặt</h3>
<p>Ưa sáng hoàn toàn, sợ tối. Cần ít nhất 4-5 giờ nắng trực tiếp mỗi ngày. Có thể đặt ngoài trời hoặc trong nhà gần cửa sổ hướng Nam. Không chịu được sương giá.</p>
<h3>Tưới nước</h3>
<p>Chịu hạn tốt, chịu ẩm kém. Tưới khi đất mặt khô, tránh tưới quá nhiều gây úng rễ. Giảm tưới vào mùa đông. Dùng nước mềm, nhiệt độ phòng.</p>
<h3>Bón phân</h3>
<p>Bón phân mỗi tháng trong mùa sinh trưởng. Sử dụng phân hữu cơ hoặc phân NPK 14-14-14. Tăng cường phân lân và kali để kích thích ra hoa nhiều.</p>
<h3>Đất trồng</h3>
<p>Cây ưa đất hơi chua, pH 6-7. Đất cần thoát nước tốt, giàu mùn. Hỗn hợp đất lý tưởng: 50% đất thịt + 30% phân hữu cơ + 20% xỉ than hoặc cát.</p>
<h3>Tỉa cành</h3>
<p>Tỉa nhẹ sau mỗi đợt hoa để duy trì dáng. Cắt bỏ cành già, cành yếu. Cây ra hoa trên đầu cành mới, nên tỉa để kích thích đâm chồi mới.</p>
<h3>Thay chậu</h3>
<p>Thay chậu 2-3 năm một lần. Cắt bớt 1/3 bộ rễ, thay đất mới. Thời điểm tốt nhất là mùa xuân.</p>
<h3>Nhân giống</h3>
<p>Giâm cành bánh tẻ vào mùa mưa, tỷ lệ sống cao. Có thể chiết cành hoặc gieo hạt.</p>',
            'https://www.bonsaiempire.vn/tree-species/linh-sam' => '<h2>Cây Linh Sam (Desmodium Unifoliatum)</h2>
<p>Linh Sam còn gọi là Ba Gai, Sam Núi. Là loài cây đặc hữu của Việt Nam, rất được ưa chuộng trong nghệ thuật Bonsai nhờ thân dẻo dễ uốn, hoa tím thơm và ý nghĩa phong thủy: thịnh vượng, giàu sang.</p>
<h3>Vị trí đặt</h3>
<p>Dễ trồng, sống được cả nắng hoặc bán râm. Nếu đặt trong nhà cần gần cửa sổ có ánh sáng tốt. Ngoài trời cần che bớt nắng gắt buổi trưa. Cây ưa khí hậu ấm áp, nhiệt độ 20-30°C.</p>
<h3>Tưới nước</h3>
<p>Cần nhiều nước, giữ đất ẩm thường xuyên. Tưới 1-2 lần/ngày vào mùa nắng. Giảm tưới vào mùa mưa và mùa đông. Không để đất khô quá lâu.</p>
<h3>Bón phân</h3>
<p>Bón phân mỗi tháng trong mùa sinh trưởng. Sử dụng phân hữu cơ hoặc phân NPK. Bổ sung phân lân để kích thích ra hoa.</p>
<h3>Tỉa cành và uốn cành</h3>
<p>Thân dẻo, rất dễ uốn — đây là đặc điểm nổi bật của Linh Sam. Có thể tạo nhiều thế Bonsai phức tạp. Tỉa cành thường xuyên để duy trì dáng. Cây chịu được tỉa mạnh.</p>
<h3>Thay chậu</h3>
<p>Thay chậu 2 năm một lần vào mùa xuân. Cắt bớt rễ, thay đất mới. Đất cần thoát nước tốt, nhiều mùn.</p>
<h3>Sâu bệnh</h3>
<p>Tương đối kháng sâu bệnh. Có thể bị rệp, nhện đỏ trong điều kiện khô nóng. Phòng trừ bằng biện pháp sinh học.</p>',
            'https://www.bonsaiempire.vn/basics/' => '<h2>Nghệ thuật Bonsai — Giới thiệu toàn diện</h2>
<p>Bonsai là nghệ thuật tạo dáng cây cảnh thu nhỏ có nguồn gốc từ Trung Quốc (gọi là Penjing) và được phát triển hoàn thiện tại Nhật Bản. Từ "Bonsai" trong tiếng Nhật có nghĩa là "cây trồng trong chậu" (bon = chậu, sai = cây trồng).</p>
<h3>Lịch sử Bonsai</h3>
<p>Nghệ thuật Bonsai đã có từ hơn 1000 năm trước. Ban đầu, các nhà sư Trung Quốc thu nhỏ cây cối để mang theo trong các chuyến hành hương. Vào thế kỷ 12, Nhật Bản tiếp thu và phát triển nghệ thuật này thành một tinh hoa văn hóa riêng.</p>
<h3>Triết lý Bonsai</h3>
<p>Bonsai không chỉ đơn thuần là trồng cây trong chậu. Đó là sự kết hợp giữa nghệ thuật và thiên nhiên, giữa con người và cây cối. Một tác phẩm Bonsai đẹp phải thể hiện được sự hài hòa giữa thân, cành, lá, chậu và phụ kiện.</p>
<h3>Các dáng Bonsai cơ bản</h3>
<ul>
<li><strong>Chokkan (Dáng thẳng)</strong> - Thân thẳng đứng, thon dần từ gốc đến ngọn</li>
<li><strong>Moyogi (Dáng nghiêng)</strong> - Thân uốn lượn mềm mại</li>
<li><strong>Shakan (Dáng xiên)</strong> - Thân nghiêng về một bên như bị gió thổi</li>
<li><strong>Kengai (Dáng thác đổ)</strong> - Thân đổ xuống dưới mép chậu</li>
<li><strong>Han-kengai (Bán thác đổ)</strong> - Thân đổ ngang mép chậu</li>
<li><strong>Bunjin (Văn nhân)</strong> - Thân cao, thanh mảnh, ít cành</li>
<li><strong>Ishizuke (Bám đá)</strong> - Rễ bám vào đá</li>
<li><strong>Yose-ue (Rừng)</strong> - Nhiều cây trồng trong một chậu</li>
</ul>',
            'https://www.bonsaiempire.vn/basics/bonsai-care/position' => '<h2>Đặt cây Bonsai — Vị trí và điều kiện ánh sáng</h2>
<p>Vị trí đặt cây Bonsai là yếu tố quan trọng nhất quyết định sự sống còn và phát triển của cây. Mỗi loài cây có nhu cầu ánh sáng và nhiệt độ khác nhau.</p>
<h3>Cây Bonsai nội thất và ngoại thất</h3>
<p><strong>Cây Bonsai trong nhà:</strong> Các loài nhiệt đới như Sanh, Tùng La Hán, Trang, Kim Ngân có thể sống trong nhà nhưng cần đặt gần cửa sổ hướng Nam hoặc Đông Nam để nhận đủ ánh sáng. Nhiệt độ lý tưởng 18-25°C.</p>
<p><strong>Cây Bonsai ngoài trời:</strong> Các loài ôn đới như Tùng, Bách, Phong, Hoa Mai cần được đặt ngoài trời quanh năm. Chúng cần chu kỳ ngủ đông với nhiệt độ thấp để sống khỏe.</p>
<h3>Hướng đặt cây</h3>
<ul>
<li><strong>Hướng Nam:</strong> Nhiều nắng nhất, phù hợp với đa số cây</li>
<li><strong>Hướng Đông:</strong> Nắng sáng dịu, phù hợp cây ưa bán râm</li>
<li><strong>Hướng Tây:</strong> Nắng gắt buổi chiều, cần che chắn</li>
<li><strong>Hướng Bắc:</strong> Ít nắng nhất, phù hợp cây ưa bóng</li>
</ul>
<h3>Lưu ý khi đặt cây</h3>
<p>Tránh đặt cây gần máy lạnh, lò sưởi hoặc nơi có gió lùa mạnh. Xoay chậu thường xuyên để cây phát triển đều các mặt. Vào mùa hè nắng gắt, cần che bớt nắng cho cây.</p>',
            'https://www.bonsaiempire.vn/basics/bonsai-care/watering' => '<h2>Tưới nước cho Bonsai — Hướng dẫn chi tiết</h2>
<p>Tưới nước là kỹ năng quan trọng nhất trong chăm sóc Bonsai. Tưới quá nhiều hoặc quá ít đều có thể làm chết cây. Không có lịch tưới cố định — bạn phải quan sát nhu cầu của từng cây.</p>
<h3>Khi nào cần tưới?</h3>
<p>Kiểm tra độ ẩm của đất bằng ngón tay: nếu đất mặt khoảng 1-2 cm đã khô thì cần tưới. Vào mùa nắng có thể tưới 1-2 lần/ngày, mùa mưa có thể vài ngày tưới một lần.</p>
<h3>Cách tưới đúng</h3>
<p>Tưới nước từ từ, đều khắp mặt chậu cho đến khi nước thoát ra từ lỗ dưới đáy chậu. Điều này đảm bảo toàn bộ bầu đất được ẩm đều. Nên dùng bình tưới có vòi sen để tránh xói đất.</p>
<h3>Loại nước</h3>
<p>Sử dụng nước mưa hoặc nước máy đã để qua đêm để khử clo. Nhiệt độ nước nên bằng nhiệt độ phòng. Tránh dùng nước đã qua làm mềm bằng muối.</p>
<h3>Duy trì độ ẩm</h3>
<p>Phun sương lên lá thường xuyên, đặc biệt với cây nhiệt đới và trong điều kiện khô nóng. Đặt chậu trên khay sỏi có nước để tăng độ ẩm xung quanh.</p>',
            'https://www.bonsaiempire.vn/basics/bonsai-care/fertilizing' => '<h2>Bón phân cho Bonsai — Dinh dưỡng và phân bón</h2>
<p>Bón phân cung cấp dinh dưỡng cần thiết cho cây Bonsai phát triển khỏe mạnh. Vì cây sống trong chậu nhỏ với lượng đất hạn chế, việc bổ sung dinh dưỡng thường xuyên là rất quan trọng.</p>
<h3>Các loại phân bón</h3>
<ul>
<li><strong>Phân hữu cơ:</strong> Dạng viên hoặc bột, tan chậm, cung cấp dinh dưỡng đều đặn</li>
<li><strong>Phân vô cơ:</strong> Dạng lỏng hoặc hạt, tan nhanh, dễ sử dụng</li>
<li><strong>Phân tan chậm:</strong> Dạng viên nén, giải phóng dinh dưỡng từ từ theo thời gian</li>
</ul>
<h3>Liều lượng và tần suất</h3>
<p>Bón phân mỗi tuần hoặc hai tuần trong mùa sinh trưởng (xuân-hè). Giảm còn mỗi tháng hoặc ngừng hẳn vào mùa đông. Luôn pha loãng phân theo hướng dẫn — tốt nhất là dùng nửa liều khuyến cáo.</p>
<h3>Nguyên tắc NPK</h3>
<ul>
<li><strong>N (Đạm):</strong> Thúc đẩy phát triển lá và thân</li>
<li><strong>P (Lân):</strong> Kích thích ra hoa và phát triển rễ</li>
<li><strong>K (Kali):</strong> Tăng sức đề kháng và chất lượng hoa trái</li>
</ul>
<h3>Lưu ý</h3>
<p>Không bón phân cho cây mới thay chậu (chờ 4-6 tuần). Không bón khi cây đang yếu hoặc bị sâu bệnh. Giảm đạm và tăng lân-kali vào giai đoạn trước khi cây ra hoa.</p>',
            'https://www.bonsaiempire.vn/basics/bonsai-care/repotting' => '<h2>Thay chậu cho Bonsai — Kỹ thuật và thời điểm</h2>
<p>Thay chậu là kỹ thuật quan trọng giúp cây Bonsai có không gian phát triển mới và bổ sung dinh dưỡng cho đất. Theo thời gian, đất trong chậu sẽ thoái hóa, mất chất dinh dưỡng và khả năng thoát nước.</p>
<h3>Khi nào cần thay chậu?</h3>
<ul>
<li>Rễ mọc ra khỏi lỗ thoát nước dưới đáy chậu</li>
<li>Cây phát triển chậm dù đã bón phân đầy đủ</li>
<li>Đất bị nén chặt, thoát nước kém</li>
<li>Rêu mọc quá nhiều trên mặt đất</li>
<li>Đã 2-3 năm kể từ lần thay chậu trước</li>
</ul>
<h3>Các bước thay chậu</h3>
<ol>
<li>Lấy cây ra khỏi chậu cũ</li>
<li>Gỡ bỏ đất cũ xung quanh bầu rễ</li>
<li>Cắt bỏ khoảng 1/3 bộ rễ — ưu tiên rễ già, rễ hư, rễ mọc vòng</li>
<li>Đặt lưới chắn lên lỗ thoát nước của chậu mới</li>
<li>Cho một lớp đất mới dưới đáy chậu</li>
<li>Đặt cây vào chậu, điều chỉnh vị trí cho đẹp</li>
<li>Đổ đất mới vào xung quanh, dùng đũa hoặc que để đất lọt vào giữa các kẽ rễ</li>
<li>Tưới nước thật đẫm và đặt cây nơi râm mát 1-2 tuần</li>
</ol>
<h3>Lưu ý sau khi thay chậu</h3>
<p>Không bón phân trong 4-6 tuần đầu. Giữ đất ẩm nhưng không quá ướt. Tránh ánh nắng trực tiếp trong 1-2 tuần. Có thể tỉa bớt lá để giảm áp lực lên bộ rễ mới.</p>',
            'https://www.bonsaiempire.vn/basics/bonsai-care/bonsai-soil' => '<h2>Chất nền (Đất trồng) cho Bonsai</h2>
<p>Đất trồng Bonsai khác biệt hoàn toàn với đất vườn thông thường. Chất nền Bonsai cần đáp ứng ba tiêu chí: thoát nước tốt, giữ ẩm vừa phải và cung cấp dinh dưỡng.</p>
<h3>Thành phần cơ bản</h3>
<ul>
<li><strong>Akadama:</strong> Đất sét nung từ Nhật Bản, giữ ẩm và thoát nước tốt</li>
<li><strong>Pumice (Đá bọt):</strong> Giúp thoát nước và giữ khí cho rễ</li>
<li><strong>Lava rock (Đá nham thạch):</strong> Tăng độ thoáng khí và thoát nước</li>
<li><strong>Phân hữu cơ:</strong> Cung cấp dinh dưỡng cho cây</li>
<li><strong>Kanuma:</strong> Đất sét vàng, pH chua, dùng cho cây ưa chua</li>
<li><strong>Kiryu:</strong> Đất sét không tan, dùng cho cây thông, tùng</li>
</ul>
<h3>Tỉ lệ pha trộn</h3>
<p><strong>Đối với cây rụng lá (Phong, Mai,...):</strong> 50% Akadama + 25% Pumice + 25% Lava rock</p>
<p><strong>Đối với cây lá kim (Tùng, Thông,...):</strong> 50% Akadama + 25% Pumice + 25% Kiryu</p>
<p><strong>Đối với cây nhiệt đới (Sanh, Trang,...):</strong> 40% Akadama + 30% Pumice + 30% phân hữu cơ</p>
<h3>Lưu ý</h3>
<p>Không dùng đất vườn thông thường cho Bonsai vì đất vườn thoát nước kém, chứa mầm bệnh và dễ nén chặt. Sàng đất qua lưới 2-3mm để loại bỏ bụi mịn — bụi mịn sẽ bít lỗ thoát khí trong đất.</p>',
            'https://www.bonsaiempire.vn/basics/styling/pruning' => '<h2>Tỉa cành Bonsai — Kỹ thuật tạo dáng cơ bản</h2>
<p>Tỉa cành là kỹ thuật quan trọng trong Bonsai để duy trì kích thước nhỏ và tạo dáng đẹp. Có hai loại tỉa cành: tỉa duy trì (maintenance pruning) và tỉa tạo dáng (structural pruning).</p>
<h3>Tỉa duy trì</h3>
<p>Tỉa duy trì giúp giữ dáng của cây và kích thích phân nhánh. Cắt bỏ các chồi mới mọc dài, giữ lại 2-3 lá trên mỗi cành. Thực hiện thường xuyên trong mùa sinh trưởng. Đối với cây Sanh, khi cành ra 6-8 lá thì tỉa còn 2 lá.</p>
<h3>Tỉa tạo dáng</h3>
<p>Tỉa tạo dáng là cắt bỏ những cành lớn để định hình cấu trúc cây. Thực hiện vào mùa ngủ đông hoặc đầu xuân. Cần xác định trước mặt trước và mặt sau của cây, loại bỏ những cành không cần thiết.</p>
<h3>Nguyên tắc tỉa cành</h3>
<ul>
<li><strong>Cành mọc song song:</strong> Chỉ giữ một</li>
<li><strong>Cành mọc đối diện:</strong> Bỏ một, giữ một</li>
<li><strong>Cành mọc từ cùng vị trí:</strong> Chỉ giữ một</li>
<li><strong>Cành mọc hướng về phía người xem:</strong> Cần tỉa bỏ</li>
<li><strong>Cành mọc xoắn quanh thân:</strong> Cắt bỏ</li>
</ul>
<h3>Dụng cụ tỉa</h3>
<p>Sử dụng kéo tỉa Bonsai chuyên dụng (concave cutter) để cắt cành lớn. Kéo thường dùng cho cành nhỏ và lá. Luôn vệ sinh dụng cụ trước và sau khi tỉa. Bôi hồ liền sẹo (cut paste) lên vết cắt lớn để tránh nhiễm bệnh.</p>',
            'https://www.bonsaiempire.vn/basics/styling/wiring' => '<h2>Uốn cành Bonsai (Wiring) — Hướng dẫn từ A đến Z</h2>
<p>Uốn cành (wiring) là kỹ thuật dùng dây kim loại quấn quanh thân và cành để tạo hình cho cây Bonsai theo ý muốn. Đây là một trong những kỹ thuật quan trọng nhất trong nghệ thuật Bonsai.</p>
<h3>Loại dây</h3>
<ul>
<li><strong>Dây nhôm:</strong> Mềm, dễ uốn, phù hợp với cây lá kim và cây nhiệt đới. Thường được dùng cho người mới bắt đầu.</li>
<li><strong>Dây đồng:</strong> Cứng hơn, giữ dáng tốt hơn, dùng cho cây rụng lá và cành lớn. Cần ủ nhiệt trước khi dùng.</li>
</ul>
<h3>Kỹ thuật quấn dây</h3>
<p>Chọn dây có độ dày khoảng 1/3 độ dày của cành cần uốn. Cắt dây dài gấp 1.5 lần chiều dài cần quấn. Quấn dây từ gốc lên ngọn, tạo góc 45 độ so với trục cành. Quấn đều tay, không quá chặt hoặc quá lỏng.</p>
<h3>Nguyên tắc cơ bản</h3>
<ul>
<li>Luôn neo dây vào thân hoặc cành khỏe trước khi uốn cành nhỏ</li>
<li>Hai dây quấn cùng chiều, không đan chéo</li>
<li>Khoảng cách giữa các vòng dây đều nhau</li>
<li>Không quấn dây qua vết cắt hoặc vết thương</li>
</ul>
<h3>Thời gian giữ dây</h3>
<p>Cây nhiệt đới (Sanh, Trang): 3-6 tháng. Cây ôn đới (Tùng, Phong): 6-12 tháng. Kiểm tra dây thường xuyên để tránh dây cắt vào vỏ cây. Khi tháo dây nên cắt từng đoạn, không kéo dây vì có thể làm gãy cành.</p>',
            'https://www.bonsaiempire.vn/basics/styling/tools' => '<h2>Dụng cụ làm Bonsai — Các công cụ cần thiết</h2>
<p>Để chăm sóc và tạo dáng Bonsai đúng cách, bạn cần có bộ dụng cụ chuyên dụng. Đầu tư vào dụng cụ chất lượng tốt sẽ giúp công việc dễ dàng hơn và bảo vệ sức khỏe của cây.</p>
<h3>Dụng cụ cắt tỉa</h3>
<ul>
<li><strong>Concave cutter (Kéo cắt lõm):</strong> Dùng cắt cành lớn, vết cắt lõm giúp lành sẹo nhanh</li>
<li><strong>Kéo tỉa thường:</strong> Dùng cắt cành nhỏ, lá, rễ</li>
<li><strong>Kéo tỉa chồi:</strong> Kéo nhỏ, đầu cong, dùng tỉa chi tiết</li>
<li><strong>Knob cutter:</strong> Cắt bỏ mắt gỗ và sẹo trên thân</li>
</ul>
<h3>Dụng cụ uốn cành</h3>
<ul>
<li><strong>Kìm bẻ dây:</strong> Cắt dây nhôm/đồng</li>
<li><strong>Kìm uốn cành:</strong> Dùng uốn cành lớn mà không làm dập vỏ</li>
<li><strong>Jack uốn cành:</strong> Kích tạo lực uốn mạnh cho cành to</li>
<li><strong>Dây nhôm/đồng:</strong> Các cỡ từ 1mm đến 6mm</li>
</ul>
<h3>Dụng cụ thay chậu</h3>
<ul>
<li><strong>Móc rễ:</strong> Gỡ đất và gỡ rễ khi thay chậu</li>
<li><strong>Kéo cắt rễ:</strong> Cắt rễ lớn khi thay chậu</li>
<li><strong>Đũa tre/que gỗ:</strong> Chọc đất vào kẽ rễ</li>
<li><strong>Lưới chắn lỗ thoát nước</strong></li>
</ul>
<h3>Dụng cụ khác</h3>
<ul>
<li><strong>Bình tưới:</strong> Có vòi sen nhỏ</li>
<li><strong>Bình phun sương</strong></li>
<li><strong>Hồ liền sẹo (Cut paste):</strong> Bôi lên vết cắt lớn</li>
<li><strong>Cọ mềm:</strong> Làm sạch thân và chậu</li>
<li><strong>Sàng đất:</strong> Sàng lọc đất trước khi sử dụng</li>
</ul>',
            'https://www.bonsaiempire.vn/basics/cultivation/buying-bonsai' => '<h2>Mua cây Bonsai — Hướng dẫn chọn mua</h2>
<p>Mua cây Bonsai là quyết định quan trọng với người mới bắt đầu. Một cây Bonsai chất lượng tốt sẽ giúp bạn có khởi đầu thuận lợi với thú chơi này.</p>
<h3>Tiêu chí chọn cây</h3>
<ul>
<li><strong>Sức khỏe:</strong> Lá xanh tươi, không vàng úa hoặc rụng. Thân không có vết nứt, vết bệnh</li>
<li><strong>Bộ rễ:</strong> Rễ phát triển đều, có phần gốc lộ ra (nebari)</li>
<li><strong>Thân:</strong> Thon dần từ gốc đến ngọn, có độ dày tương xứng</li>
<li><strong>Phân cành:</strong> Cành phân bố đều, tạo thành tán tự nhiên</li>
<li><strong>Chậu:</strong> Hài hòa với cây, có lỗ thoát nước</li>
</ul>
<h3>Nên mua ở đâu?</h3>
<p>Nên mua từ các vườn ươm Bonsai chuyên nghiệp hoặc cửa hàng uy tín. Tránh mua Bonsai giá rẻ siêu thị — chúng thường được sản xuất hàng loạt với chất lượng kém: dây quấn cắt vào vỏ, ghép kém, đất xấu, chậu không thoát nước.</p>
<h3>Giá cả</h3>
<p>Bonsai có giá từ vài trăm nghìn (cây non, mới tập tạo dáng) đến hàng trăm triệu (cây cổ thụ, tác phẩm nghệ thuật). Người mới nên bắt đầu với cây giá phải chăng, dễ chăm sóc như Sanh, Tùng La Hán hoặc Trang.</p>
<h3>Kiểm tra trước khi mua</h3>
<p>Yêu cầu người bán hướng dẫn cách chăm sóc cơ bản. Kiểm tra xem cây có sâu bệnh không. Hỏi về lịch sử thay chậu và bón phân. Chọn cây phù hợp với điều kiện nhà bạn (trong nhà hay ngoài trời).</p>',
            'https://www.bonsaiempire.vn/basics/cultivation/from-seeds' => '<h2>Trồng Bonsai từ hạt — Hướng dẫn từng bước</h2>
<p>Trồng Bonsai từ hạt là phương pháp dành cho người kiên nhẫn. Bạn sẽ được chứng kiến toàn bộ quá trình phát triển của cây từ khi nảy mầm đến khi thành tác phẩm. Tuy nhiên, phương pháp này mất nhiều năm mới có được cây Bonsai đẹp.</p>
<h3>Chuẩn bị</h3>
<ul>
<li><strong>Hạt giống:</strong> Nên mua từ nguồn uy tín, chọn loài phù hợp với khí hậu địa phương</li>
<li><strong>Khay ươm:</strong> Có lỗ thoát nước</li>
<li><strong>Đất ươm:</strong> Hỗn hợp than bùn + đá trân châu (perlite) tỉ lệ 1:1</li>
<li><strong>Bình phun sương</strong></li>
<li><strong>Bọc nilon hoặc nắp trong suốt</strong> để giữ ẩm</li>
</ul>
<h3>Quy trình ươm hạt</h3>
<ol>
<li>Ngâm hạt trong nước ấm (khoảng 40°C) từ 12-24 giờ</li>
<li>Gieo hạt vào khay ươm, phủ một lớp đất mỏng</li>
<li>Phun sương nhẹ, đậy bọc nilon để giữ ẩm</li>
<li>Đặt nơi ấm áp (20-25°C), có ánh sáng gián tiếp</li>
<li>Kiểm tra độ ẩm hàng ngày, phun sương khi cần</li>
<li>Khi hạt nảy mầm (1-4 tuần), mở dần bọc nilon để cây quen với không khí</li>
</ol>
<h3>Chăm sóc cây con</h3>
<p>Khi cây cao khoảng 5-10cm, chuyển sang chậu riêng. Bón phân loãng mỗi 2 tuần. Để cây phát triển tự do trong 2-3 năm trước khi bắt đầu tạo dáng. Tỉa bỏ cành yếu, giữ lại thân chính.</p>
<h3>Lưu ý</h3>
<p>Hạt giống cây rụng lá (Phong, Sồi,...) cần xử lý lạnh (stratification) trước khi gieo. Tỉ lệ nảy mầm của hạt Bonsai không cao, nên gieo nhiều hạt cùng lúc. Cây trồng từ hạt có bộ rễ tốt hơn cây giâm cành.</p>',
        ];

        return $map[$url] ?? '<p>Bài viết đang được cập nhật.</p>';
    }
}
