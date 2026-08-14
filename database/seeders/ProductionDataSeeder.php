<?php

/**
 * @deprecated Không còn sử dụng. Dữ liệu ảo (fake sellers, buyers, orders, reviews, ...)
 * đã được thay thế bằng dữ liệu thật từ BonsaiEmpire.vn.
 * Giữ lại để tham khảo. Sẽ xóa trong phiên bản tới.
 *
 * @see TreeSpeciesSeeder
 * @see CareGuideSeeder
 * @see BonsaiStyleSeeder
 */

namespace Database\Seeders;

use App\Models\User;
use App\Modules\AgriVerse\Models\Coupon;
use App\Modules\AgriVerse\Models\ForumCategory;
use App\Modules\AgriVerse\Models\ForumPost;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Review;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ProductionDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->warn('⚠️  ProductionDataSeeder đã bị deprecated. Không chạy dữ liệu ảo.');
    }
}
