<template>
  <div class="min-h-screen flex flex-col" style="background-color: var(--ag-bg);">
    <Toast position="top-right" />
    <ChatPanel />

    <!-- ========== HEADER: Glassmorphism Sticky Nav ========== -->
    <header ref="headerRef"
      class="header"
      :class="{ 'header-scrolled': scrolled }">
      <div class="header-container">
        <!-- Mobile Menu Toggle -->
        <button @click="mobileOpen = !mobileOpen"
          class="md:hidden w-10 h-10 flex items-center justify-center rounded-lg header-icon-btn">
          <span class="material-symbols-outlined text-xl">{{ mobileOpen ? 'close' : 'menu' }}</span>
        </button>

        <!-- Left Group: Logo + Nav -->
        <div class="flex items-center gap-12">
          <!-- Brand: Roboto -->
        <Link :href="route('agriverse.shop.home')" class="flex items-center gap-2.5 shrink-0 group">
          <div class="w-8 h-8 rounded-lg header-logo-mark flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
            <span class="material-symbols-outlined text-white" style="font-size: 18px;">eco</span>
          </div>
          <span class="brand-text hidden sm:block italic" style="font-size: 24px;">AgriVerse</span>
        </Link>

        <!-- Navigation: Roboto -->
        <nav class="hidden md:flex items-center gap-8 nav-text">
          <Link v-for="item in desktopNav" :key="item.route" :href="route(item.route)"
            class="px-3 py-2 rounded-lg nav-link transition-all duration-200"
            :class="route().current(item.pattern)
              ? 'nav-link-active'
              : ''">
            {{ item.label }}
          </Link>
        </nav>
        </div>

        <!-- Right Group: Search + Icons + Auth -->
        <div class="flex items-center gap-6 flex-1 justify-end">
        <!-- Search -->
        <form @submit.prevent="search" class="flex-1 max-w-[480px] hidden sm:block">
          <div class="relative">
            <input v-model="searchQuery" type="text" placeholder="Tìm cây cảnh, phụ kiện..."
              class="w-full h-10 pl-4 pr-10 rounded-lg search-input text-sm outline-none transition-all duration-200">
            <button type="submit" class="absolute right-0 top-0 w-10 h-10 flex items-center justify-center search-btn transition-colors duration-200">
              <span class="material-symbols-outlined" style="font-size: 20px;">search</span>
            </button>
          </div>
        </form>

        <!-- Icons -->
        <div class="flex items-center gap-0.5 md:gap-0.5">
          <!-- Mobile search toggle -->
          <button @click="mobileSearchOpen = !mobileSearchOpen"
            class="md:hidden w-9 h-9 flex items-center justify-center rounded-lg header-icon-btn"
            title="Tìm kiếm">
            <span class="material-symbols-outlined" style="font-size: 20px;">search</span>
          </button>

          <Link :href="route('agriverse.shop.wishlist.index')"
            class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-lg header-icon-btn"
            :class="{ 'wishlist-active': route().current('agriverse.shop.wishlist.*') }"
            title="Yêu thích">
            <span class="material-symbols-outlined" style="font-size: 20px;">favorite</span>
          </Link>

          <Link :href="route('agriverse.shop.cart.index')"
            class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-lg header-icon-btn relative"
            :class="{ 'cart-active': route().current('agriverse.shop.cart.*') }"
            title="Giỏ hàng">
            <span class="material-symbols-outlined" style="font-size: 20px;">shopping_bag</span>
            <span v-if="cartCount > 0"
              class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] rounded-full cart-badge flex items-center justify-center px-[4px] text-[10px] font-bold animate-scale-in">
              {{ cartCount > 99 ? '99+' : cartCount }}
            </span>
          </Link>

          <!-- Chat -->
          <button @click="openChatPanel"
            class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-lg header-icon-btn relative"
            :class="{ 'chat-active': chatState.panelOpen }"
            title="Tin nhắn">
            <span class="material-symbols-outlined" style="font-size: 20px;">chat</span>
            <span v-if="totalUnread > 0"
              class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] rounded-full cart-badge flex items-center justify-center px-[4px] text-[10px] font-bold animate-scale-in">
              {{ totalUnread > 99 ? '99+' : totalUnread }}
            </span>
          </button>
        </div>

        <!-- Auth -->
        <div class="flex items-center gap-1 pl-3 header-divider nav-text">
          <template v-if="isAuthenticated">
            <div class="relative" ref="userMenuRef">
              <button @click="userMenuOpen = !userMenuOpen; mobileSearchOpen = false"
                class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-full header-user-btn transition-all duration-200"
                title="Tài khoản">
                <span v-if="user?.avatar_url" class="w-full h-full rounded-full overflow-hidden">
                  <img :src="user.avatar_url" :alt="user.name" class="w-full h-full object-cover" />
                </span>
                <span v-else class="w-full h-full rounded-full flex items-center justify-center text-xs md:text-sm font-bold header-user-avatar">
                  {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                </span>
              </button>

              <Transition name="dropdown">
                <div v-if="userMenuOpen"
                  class="absolute right-0 top-full mt-2 w-64 md:w-60 rounded-xl shadow-lg border user-dropdown z-50 overflow-hidden">
                  <div class="px-4 py-3 user-dropdown-header">
                    <p class="text-sm font-semibold truncate" style="color: var(--ag-on-surface);">{{ user?.name || 'Người dùng' }}</p>
                    <p class="text-xs truncate mt-0.5" style="color: var(--ag-on-surface-variant);">{{ user?.email || '' }}</p>
                  </div>
                  <div class="h-px" style="background-color: var(--ag-border);" />
                  <div class="py-1">
                    <!-- Seller Management (only for approved sellers) -->
                    <template v-if="isSeller">
                      <Link :href="route('agriverse.shop.seller.dashboard')"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm transition-all duration-200 user-dropdown-item"
                        @click="userMenuOpen = false">
                        <span class="material-symbols-outlined text-lg" style="color: var(--ag-primary-500);">store</span>
                        <span>Quản lý</span>
                        <span class="ml-auto text-[10px] px-1.5 py-0.5 rounded-full font-bold" style="background: var(--ag-primary-500); color: white; font-size: 9px;">SELLER</span>
                      </Link>
                      <div class="h-px mx-4" style="background-color: var(--ag-border);" />
                    </template>
                    <!-- Become Seller (only for non-sellers) -->
                    <template v-else-if="!isSeller && canBecomeSeller">
                      <Link :href="route('agriverse.shop.seller.register')"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold transition-all duration-200 user-dropdown-item"
                        @click="userMenuOpen = false"
                        style="color: var(--ag-primary-500);">
                        <span class="material-symbols-outlined text-lg">storefront</span>
                        <span>Đăng ký người bán</span>
                        <span class="ml-auto text-[9px] px-1.5 py-0.5 rounded-full font-bold animate-pulse" style="background: var(--ag-primary-500); color: white;">MỚI</span>
                      </Link>
                      <div class="h-px mx-4" style="background-color: var(--ag-border);" />
                    </template>

                    <Link :href="route('agriverse.shop.profile.index')"
                      class="flex items-center gap-3 px-4 py-2.5 text-sm transition-all duration-200 user-dropdown-item"
                      @click="userMenuOpen = false">
                      <span class="material-symbols-outlined text-lg" style="color: var(--ag-text-muted);">person</span>
                      <span>Hồ sơ</span>
                    </Link>
                    <Link :href="route('agriverse.shop.orders.index')"
                      class="flex items-center gap-3 px-4 py-2.5 text-sm transition-all duration-200 user-dropdown-item"
                      @click="userMenuOpen = false">
                      <span class="material-symbols-outlined text-lg" style="color: var(--ag-text-muted);">receipt_long</span>
                      <span>Đơn hàng</span>
                    </Link>
                    <Link :href="route('agriverse.shop.addresses.index')"
                      class="flex items-center gap-3 px-4 py-2.5 text-sm transition-all duration-200 user-dropdown-item"
                      @click="userMenuOpen = false">
                      <span class="material-symbols-outlined text-lg" style="color: var(--ag-text-muted);">location_on</span>
                      <span>Địa chỉ</span>
                    </Link>
                    <Link :href="route('agriverse.shop.account.settings')"
                      class="flex items-center gap-3 px-4 py-2.5 text-sm transition-all duration-200 user-dropdown-item"
                      @click="userMenuOpen = false">
                      <span class="material-symbols-outlined text-lg" style="color: var(--ag-text-muted);">settings</span>
                      <span>Cài đặt</span>
                    </Link>
                  </div>
                  <div class="h-px" style="background-color: var(--ag-border);" />
                  <div class="py-1">
                    <button @click="handleLogoutFromMenu"
                      class="flex items-center gap-3 px-4 py-2.5 text-sm transition-all duration-200 w-full text-left user-dropdown-logout">
                      <span class="material-symbols-outlined text-lg">logout</span>
                      <span>Đăng xuất</span>
                    </button>
                  </div>
                </div>
              </Transition>
            </div>
          </template>
          <template v-else>
            <button @click="goToLogin"
              class="px-3 py-2 rounded-lg text-sm header-secondary-link transition-all duration-200">
              Đăng nhập
            </button>
            <button @click="goToRegister"
              class="px-4 py-2 rounded-lg header-register-btn text-sm font-semibold transition-all duration-200 active:scale-[0.97]">
              Đăng ký
            </button>
          </template>
        </div>
        </div>
      </div>

      <!-- Mobile Search Bar -->
      <Transition name="mobile-search">
        <div v-if="mobileSearchOpen" class="md:hidden mobile-search-bar">
          <form @submit.prevent="search" class="mobile-search-form">
            <span class="material-symbols-outlined mobile-search-icon">search</span>
            <input v-model="searchQuery" type="text" placeholder="Tìm cây cảnh, phụ kiện..."
              class="mobile-search-input" autofocus />
            <button type="button" @click="mobileSearchOpen = false" class="mobile-search-close">
              <span class="material-symbols-outlined" style="font-size: 18px;">close</span>
            </button>
          </form>
        </div>
      </Transition>

      <!-- Mobile Menu Drawer -->
      <Transition name="mobile-menu">
        <div v-if="mobileOpen" class="fixed inset-0 z-40 md:hidden" @click="mobileOpen = false">
          <div class="absolute inset-0 mobile-overlay" />
          <div class="absolute left-0 top-0 bottom-0 w-80 mobile-drawer" @click.stop>
            <div class="h-16 flex items-center px-6" style="border-bottom: 1px solid var(--ag-border);">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg header-logo-mark flex items-center justify-center">
                  <span class="material-symbols-outlined text-white" style="font-size: 18px;">eco</span>
                </div>
                <div>
                  <div class="brand-text" style="font-size: 18px;">AgriVerse</div>
                  <div class="text-[11px]" style="color: var(--ag-text-muted); font-family: var(--ag-font-body);">Chợ cây cảnh trực tuyến</div>
                </div>
              </div>
            </div>
            <nav class="py-4">
              <Link v-for="item in mobileNav" :key="item.route" :href="route(item.route)"
                class="flex items-center gap-4 px-6 py-3 text-sm transition-all duration-200 mobile-nav-item"
                :class="route().current(item.pattern) ? 'mobile-nav-item-active' : ''"
                @click="mobileOpen = false">
                <span class="material-symbols-outlined text-xl mobile-nav-icon" :class="route().current(item.pattern) ? 'mobile-nav-icon-active' : ''">{{ item.icon }}</span>
                {{ item.label }}
              </Link>
              <button @click="openChatFromMobile"
                class="flex items-center gap-4 px-6 py-3 text-sm transition-all duration-200 w-full text-left mobile-nav-item">
                <span class="material-symbols-outlined text-xl mobile-nav-icon" style="color: var(--ag-text-muted);">chat</span>
                Tin nhắn
              </button>
              <div class="h-px my-3 mx-6" style="background-color: var(--ag-border);" />
              <template v-if="isAuthenticated">
                <!-- User Info -->
                <div class="px-6 py-4 flex items-center gap-3" style="border-bottom: 1px solid var(--ag-border);">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold" style="background: var(--ag-primary-500); color: white;">
                    {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate" style="color: var(--ag-on-surface);">{{ user?.name || 'Người dùng' }}</p>
                    <p class="text-xs truncate" style="color: var(--ag-on-surface-variant);">{{ user?.email || '' }}</p>
                  </div>
                </div>
                <div class="py-1">
                  <Link :href="route('agriverse.shop.profile.index')"
                    class="flex items-center gap-4 px-6 py-3 text-sm transition-all duration-200 mobile-nav-item"
                    @click="mobileOpen = false">
                    <span class="material-symbols-outlined text-xl" style="color: var(--ag-text-muted);">person</span>
                    Hồ sơ
                  </Link>
                  <Link :href="route('agriverse.shop.orders.index')"
                    class="flex items-center gap-4 px-6 py-3 text-sm transition-all duration-200 mobile-nav-item"
                    @click="mobileOpen = false">
                    <span class="material-symbols-outlined text-xl" style="color: var(--ag-text-muted);">receipt_long</span>
                    Đơn hàng
                  </Link>
                  <Link :href="route('agriverse.shop.addresses.index')"
                    class="flex items-center gap-4 px-6 py-3 text-sm transition-all duration-200 mobile-nav-item"
                    @click="mobileOpen = false">
                    <span class="material-symbols-outlined text-xl" style="color: var(--ag-text-muted);">location_on</span>
                    Địa chỉ
                  </Link>
                  <Link :href="route('agriverse.shop.account.settings')"
                    class="flex items-center gap-4 px-6 py-3 text-sm transition-all duration-200 mobile-nav-item"
                    @click="mobileOpen = false">
                    <span class="material-symbols-outlined text-xl" style="color: var(--ag-text-muted);">settings</span>
                    Cài đặt
                  </Link>
                </div>
                <div class="h-px mx-6" style="background-color: var(--ag-border);" />
                <div class="py-1">
                  <button @click="handleLogoutMobile"
                    class="flex items-center gap-4 px-6 py-3 text-sm transition-all duration-200 w-full text-left mobile-logout-btn">
                    <span class="material-symbols-outlined text-xl">logout</span>
                    Đăng xuất
                  </button>
                </div>
              </template>
              <template v-else>
                <button @click="goToLoginMobile"
                  class="flex items-center gap-4 px-6 py-3 text-sm transition-all duration-200 mobile-nav-item">
                  <span class="material-symbols-outlined text-xl" style="color: var(--ag-text-muted);">login</span>
                  Đăng nhập
                </button>
                <button @click="goToRegisterMobile"
                  class="flex items-center gap-4 px-6 py-3 text-sm font-semibold transition-all duration-200 mobile-nav-item"
                  style="color: var(--ag-primary-500);">
                  <span class="material-symbols-outlined text-xl">person_add</span>
                  Đăng ký
                </button>
              </template>
            </nav>
          </div>
        </div>
      </Transition>
    </header>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- ========== FOOTER ========== -->
    <footer class="footer">
      <div class="footer-container">
        <div class="grid grid-cols-12 gap-8 md:gap-12">
          <!-- Brand Column -->
          <div class="col-span-12 md:col-span-4">
            <div class="flex items-center gap-2.5 mb-4">
              <div class="w-8 h-8 rounded-lg header-logo-mark flex items-center justify-center">
                <span class="material-symbols-outlined text-white" style="font-size: 18px;">spa</span>
              </div>
              <span class="brand-text" style="font-size: 20px;">AgriVerse</span>
            </div>
            <p class="footer-description">
              Cộng đồng bonsai &amp; cây cảnh Việt Nam &mdash; nơi hội tụ của những người yêu cây, chia sẻ kiến thức và kết nối vườn ươm uy tín trên toàn quốc.
            </p>
            <div class="flex items-center gap-3 mt-5">
              <a href="#" class="footer-social-link">
                <span class="material-symbols-outlined" style="font-size: 18px;">groups</span>
              </a>
              <a href="#" class="footer-social-link">
                <span class="material-symbols-outlined" style="font-size: 18px;">movie</span>
              </a>
              <a href="#" class="footer-social-link">
                <span class="material-symbols-outlined" style="font-size: 18px;">call</span>
              </a>
            </div>
          </div>

          <!-- Khám phá -->
          <div class="col-span-6 md:col-span-2">
            <div class="footer-heading">Khám phá</div>
            <div class="space-y-3">
              <Link :href="route('agriverse.shop.products.index')" class="block text-sm footer-link">Cây cảnh</Link>
              <Link :href="route('agriverse.shop.products.index')" class="block text-sm footer-link">Phụ kiện</Link>
              <Link :href="route('agriverse.shop.categories.index')" class="block text-sm footer-link">Danh mục</Link>
              <a href="#" class="block text-sm footer-link">Ưu đãi đặc biệt</a>
            </div>
          </div>

          <!-- Kiến thức -->
          <div class="col-span-6 md:col-span-2">
            <div class="footer-heading">Kiến thức</div>
            <div class="space-y-3">
              <Link :href="route('agriverse.shop.garden.index')" class="block text-sm footer-link">Khu vườn của tôi</Link>
              <Link :href="route('agriverse.shop.quiz.index')" class="block text-sm footer-link">Trắc nghiệm cây cảnh</Link>
              <Link :href="route('agriverse.shop.diagnostic.index')" class="block text-sm footer-link">Chẩn đoán cây</Link>
            </div>
          </div>

          <!-- Newsletter -->
          <div class="col-span-12 md:col-span-4">
            <div class="footer-heading">Bản tin xanh</div>
            <p class="footer-description mb-4">
              Nhận bí quyết chăm sóc cây cảnh, mẹo từ nghệ nhân và cập nhật bộ sưu tập mới mỗi tuần.
            </p>
            <div class="flex items-center border-b border-outline/30 pb-2" style="border-bottom-color: rgba(116, 121, 108, 0.3);">
              <input type="email" placeholder="Nhập email của bạn"
                class="flex-1 bg-transparent border-none text-sm outline-none shadow-none focus:ring-0 placeholder-muted p-0 h-8">
              <button type="button" class="text-primary hover:opacity-80 transition-opacity flex items-center justify-center h-8" style="color: var(--ag-primary-500);">
                <span class="material-symbols-outlined" style="font-size: 24px;">arrow_forward</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Copyright -->
        <div class="footer-copyright">
          &copy; {{ new Date().getFullYear() }} AgriVerse. Cộng đồng bonsai &amp; cây cảnh Việt.
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useAuth } from '@agriverse/Composables/useAuth';
import { useChat } from '@agriverse/Composables/useChat';
import { useChatSocket } from '@agriverse/Composables/useChatSocket';
import ChatPanel from '@agriverse/Components/ChatPanel.vue';
import Toast from 'primevue/toast';

const { user, isAuthenticated, logout, syncFromPageProps } = useAuth();
const { state: chatState, togglePanel: toggleChatPanel } = useChat();
const { connect, on } = useChatSocket();

const searchQuery = ref('');
const totalUnread = ref(0);
const mobileOpen = ref(false);
const mobileSearchOpen = ref(false);
const scrolled = ref(false);
const headerRef = ref(null);
const userMenuOpen = ref(false);
const userMenuRef = ref(null);
const page = usePage();

function onScroll() {
  scrolled.value = window.scrollY > 20;
}

function onClickOutside(e) {
  if (userMenuOpen.value && userMenuRef.value && !userMenuRef.value.contains(e.target)) {
    userMenuOpen.value = false;
  }
}

function handleLogoutFromMenu() {
  userMenuOpen.value = false;
  handleLogout();
}

function openChatPanel() {
  totalUnread.value = 0;
  toggleChatPanel();
}

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true });
  document.addEventListener('click', onClickOutside);
  syncFromPageProps();
  connect();
  on('message', (data) => {
    if (data && data.type === 'private' && !chatState.panelOpen) {
      totalUnread.value++
    }
  });
});
onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll);
  document.removeEventListener('click', onClickOutside);
});

const cartCount = computed(() => page.props.cartCount ?? 0);

const isSeller = computed(() => page.props.auth?.user?.role === 'seller')

const canBecomeSeller = computed(() => {
  return !isSeller.value && page.props.auth?.user?.role !== 'admin'
})

const desktopNav = [
  { label: 'Sản phẩm', route: 'agriverse.shop.products.index', pattern: 'agriverse.shop.products.*' },
  { label: 'Cửa hàng', route: 'agriverse.shop.stores.index', pattern: 'agriverse.shop.stores.*' },
  { label: 'Khu vườn', route: 'agriverse.shop.garden.index', pattern: 'agriverse.shop.garden.*' },
  { label: 'Diễn đàn', route: 'agriverse.shop.forum.index', pattern: 'agriverse.shop.forum.*' },
];

const mobileNav = [
  { label: 'Trang chủ', icon: 'home', route: 'agriverse.shop.home', pattern: 'agriverse.shop.home' },
  { label: 'Sản phẩm', icon: 'inventory_2', route: 'agriverse.shop.products.index', pattern: 'agriverse.shop.products.*' },
  { label: 'Cửa hàng', icon: 'storefront', route: 'agriverse.shop.stores.index', pattern: 'agriverse.shop.stores.*' },
  { label: 'Khu vườn', icon: 'forest', route: 'agriverse.shop.garden.index', pattern: 'agriverse.shop.garden.*' },
  { label: 'Diễn đàn', icon: 'forum', route: 'agriverse.shop.forum.index', pattern: 'agriverse.shop.forum.*' },
  { label: 'Yêu thích', icon: 'favorite', route: 'agriverse.shop.wishlist.index', pattern: 'agriverse.shop.wishlist.*' },
  { label: 'Giỏ hàng', icon: 'shopping_bag', route: 'agriverse.shop.cart.index', pattern: 'agriverse.shop.cart.*' },
];

function goToLogin() {
  window.location.href = '/login';
}
function goToRegister() {
  window.location.href = '/register';
}
function openChatFromMobile() {
  mobileOpen.value = false;
  openChatPanel();
}

function goToLoginMobile() {
  mobileOpen.value = false;
  window.location.href = '/login';
}
function goToRegisterMobile() {
  mobileOpen.value = false;
  window.location.href = '/register';
}

function search() {
  if (searchQuery.value.trim()) {
    router.get(route('agriverse.shop.products.index', { search: searchQuery.value.trim() }));
  }
}

async function handleLogout() {
  await logout();
}

function handleLogoutMobile() {
  mobileOpen.value = false;
  handleLogout();
}

</script>

<style scoped>
/* ============================================================
   BOTANICAL HERITAGE DESIGN SYSTEM — MarketplaceLayout
   ============================================================
   Typography: Roboto
   Palette: Sage Green (#486730) + Terracotta (#8b4f27) + Sand (#f4f1ea)
   Container: 1280px, 8px base grid, 24px gutter
   ============================================================ */

/* ===== Brand Typography ===== */
.brand-text {
  font-family: var(--ag-font-display);
  font-weight: 500;
  color: var(--ag-primary-500);
  letter-spacing: -0.02em;
}

.nav-text {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 500;
}

/* ===== Header ===== */
.header {
  position: sticky;
  top: 0;
  z-index: 50;
  background: rgba(252, 249, 248, 0.7);
  backdrop-filter: blur(12px) saturate(1.4);
  -webkit-backdrop-filter: blur(12px) saturate(1.4);
  border-bottom: 1px solid rgba(234, 231, 231, 0.5);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.header-scrolled {
  background: rgba(252, 249, 248, 0.8);
  backdrop-filter: blur(20px) saturate(1.5);
  -webkit-backdrop-filter: blur(20px) saturate(1.5);
  border-bottom: 1px solid rgba(234, 231, 231, 0.8);
  box-shadow: 0 1px 3px rgba(72, 103, 48, 0.04);
}
.header-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 64px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
@media (max-width: 768px) {
  .header-container {
    padding: 0 16px;
    height: 56px;
    gap: 8px;
  }
}
.header-logo-mark {
  background: var(--ag-primary-500);
}
.header-icon-btn {
  color: var(--ag-text-secondary);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.header-icon-btn:hover {
  color: var(--ag-primary-500);
  background: color-mix(in srgb, var(--ag-primary-500) 6%, transparent);
}
.header-icon-btn:active {
  transform: scale(0.93);
}
.wishlist-active {
  color: var(--ag-danger) !important;
}
.wishlist-active:hover {
  background: color-mix(in srgb, var(--ag-danger) 6%, transparent) !important;
}
.cart-active {
  color: var(--ag-primary-500) !important;
}
.cart-badge {
  background: var(--ag-secondary-500);
  color: white;
}
.nav-link {
  display: inline-flex;
  align-items: center;
  color: var(--ag-text-secondary);
}
.nav-link:hover {
  color: var(--ag-primary-500);
  background: color-mix(in srgb, var(--ag-primary-500) 6%, transparent);
}
.nav-link-active {
  color: var(--ag-primary-600);
  background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);
  font-weight: 600;
}
.header-secondary-link {
  color: var(--ag-text-secondary);
}
.header-secondary-link:hover {
  color: var(--ag-primary-500);
  background: color-mix(in srgb, var(--ag-primary-500) 6%, transparent);
}
.header-logout-btn {
  color: var(--ag-text-muted);
}
.header-logout-btn:hover {
  color: var(--ag-danger);
  background: color-mix(in srgb, var(--ag-danger) 6%, transparent);
}
.header-register-btn {
  background: var(--ag-primary-500);
  color: white;
}
.header-register-btn:hover {
  background: var(--ag-primary-600);
  box-shadow: 0 4px 12px -2px color-mix(in srgb, var(--ag-primary-500) 30%, transparent);
}
.header-divider {
  border-left: 1px solid var(--ag-border);
}

/* ===== User Avatar Button ===== */
.header-user-btn {
  background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);
  color: var(--ag-primary-500);
  border: 2px solid transparent;
}
.header-user-btn:hover {
  background: color-mix(in srgb, var(--ag-primary-500) 12%, transparent);
  border-color: color-mix(in srgb, var(--ag-primary-500) 20%, transparent);
}
.header-user-avatar {
  background: var(--ag-primary-500);
  color: white;
}

/* ===== User Dropdown ===== */
.user-dropdown {
  background: white;
  border-color: var(--ag-border);
  box-shadow: 0 12px 40px -8px rgba(27, 28, 28, 0.12);
}
.user-dropdown-header {
  background: var(--ag-neutral-50);
}
.user-dropdown-item {
  color: var(--ag-text-secondary);
  font-family: var(--ag-font-body);
}
.user-dropdown-item:hover {
  color: var(--ag-text-primary);
  background: color-mix(in srgb, var(--ag-primary-500) 4%, transparent);
}
.user-dropdown-logout {
  color: var(--ag-danger);
  font-family: var(--ag-font-body);
}
.user-dropdown-logout:hover {
  background: color-mix(in srgb, var(--ag-danger) 5%, transparent);
}

/* Dropdown Transition */
.dropdown-enter-active { transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
.dropdown-leave-active { transition: all 0.15s ease; }
.dropdown-enter-from { opacity: 0; transform: translateY(-8px) scale(0.96); }
.dropdown-leave-to { opacity: 0; transform: translateY(-4px) scale(0.97); }

/* ===== Search ===== */
.search-input {
  border: 1px solid var(--ag-border);
  background: var(--ag-neutral-100);
  color: var(--ag-text-primary);
  font-family: var(--ag-font-body);
}
.search-input::placeholder {
  color: var(--ag-text-muted);
}
.search-input:hover {
  border-color: var(--ag-neutral-300);
}
.search-input:focus {
  border-color: var(--ag-primary-500);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--ag-primary-500) 10%, transparent);
  background: white;
}
.search-btn {
  color: var(--ag-text-muted);
}
.search-btn:hover {
  color: var(--ag-primary-500);
}

/* ===== Mobile Search ===== */
.mobile-search-bar {
  position: sticky;
  top: 56px;
  z-index: 49;
  background: var(--ag-bg);
  border-bottom: 1px solid var(--ag-border);
  padding: 8px 16px;
}
.mobile-search-form {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--ag-neutral-100);
  border: 1px solid var(--ag-border);
  border-radius: 10px;
  padding: 0 12px;
  height: 40px;
}
.mobile-search-icon {
  font-size: 18px;
  color: var(--ag-text-muted);
  flex-shrink: 0;
}
.mobile-search-input {
  flex: 1;
  background: transparent;
  border: none;
  font: 400 14px/1 var(--ag-font-body);
  color: var(--ag-text-primary);
  outline: none;
  min-width: 0;
}
.mobile-search-input::placeholder {
  color: var(--ag-text-muted);
}
.mobile-search-close {
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: color-mix(in srgb, var(--ag-text-muted) 10%, transparent);
  border-radius: 50%;
  color: var(--ag-text-secondary);
  cursor: pointer;
  flex-shrink: 0;
}
.mobile-search-enter-active { transition: all 0.2s ease; }
.mobile-search-leave-active { transition: all 0.15s ease; }
.mobile-search-enter-from, .mobile-search-leave-to {
  opacity: 0;
  transform: translateY(-8px);
  max-height: 0;
}
.mobile-search-enter-to, .mobile-search-leave-from {
  max-height: 60px;
}

/* ===== Mobile Menu ===== */
.mobile-menu-enter-active { transition: opacity 0.3s ease; }
.mobile-menu-leave-active { transition: opacity 0.2s ease; }
.mobile-menu-enter-from, .mobile-menu-leave-to { opacity: 0; }
.mobile-menu-enter-active > div:last-child { transition: transform 0.35s cubic-bezier(0.32, 0.72, 0, 1); }
.mobile-menu-leave-active > div:last-child { transition: transform 0.25s ease; }
.mobile-menu-enter-from > div:last-child { transform: translateX(-100%); }
.mobile-menu-leave-to > div:last-child { transform: translateX(-100%); }
.mobile-overlay {
  background: color-mix(in srgb, var(--ag-neutral-900) 20%, transparent);
  backdrop-filter: blur(4px);
}
.mobile-drawer {
  background: white;
}
.mobile-nav-item {
  color: var(--ag-text-secondary);
  font-family: var(--ag-font-body);
}
.mobile-nav-item:hover {
  color: var(--ag-text-primary);
  background: color-mix(in srgb, var(--ag-primary-500) 4%, transparent);
}
.mobile-nav-item-active {
  color: var(--ag-primary-600);
  background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);
  font-weight: 600;
}
.mobile-nav-icon {
  color: var(--ag-text-muted);
}
.mobile-nav-icon-active {
  color: var(--ag-primary-500);
}
.mobile-logout-btn {
  color: var(--ag-danger);
  font-family: var(--ag-font-body);
}
.mobile-logout-btn:hover {
  background: color-mix(in srgb, var(--ag-danger) 5%, transparent);
}

/* ===== Footer: Botanical Heritage Sand/Bone ===== */
.footer {
  background: var(--ag-surface-container, #f0eded);
  border-top: 1px solid var(--ag-outline-10, rgba(116, 121, 108, 0.1));
  margin-top: 80px;
}
.footer-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 80px 64px 40px;
}
@media (max-width: 768px) {
  .footer-container { padding: 64px 24px 40px; }
}
.footer-heading {
  font-family: var(--ag-font-body);
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--ag-neutral-700);
  margin-bottom: 16px;
}
.footer-description {
  font-family: var(--ag-font-body);
  font-size: 14px;
  line-height: 22px;
  color: var(--ag-text-secondary);
  max-width: 320px;
}
.footer-link {
  color: var(--ag-text-secondary);
  font-family: var(--ag-font-body);
  transition: color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.footer-link:hover {
  color: var(--ag-primary-500);
}
.footer-social-link {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: color-mix(in srgb, var(--ag-primary-500) 10%, transparent);
  color: var(--ag-primary-500);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.footer-social-link:hover {
  background: var(--ag-primary-500);
  color: white;
}
.footer-newsletter-input {
  border: 1px solid var(--ag-accent-300, #ddd6ca);
  background: white;
  font-family: var(--ag-font-body);
  color: var(--ag-text-primary);
}
.footer-newsletter-input::placeholder {
  color: var(--ag-text-muted);
}
.footer-newsletter-input:focus {
  border-color: var(--ag-primary-500);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--ag-primary-500) 10%, transparent);
}
.footer-newsletter-btn {
  background: var(--ag-primary-500);
  color: white;
  border: none;
  cursor: pointer;
  font-family: var(--ag-font-body);
  font-weight: 600;
}
.footer-newsletter-btn:hover {
  background: var(--ag-primary-600);
}
.footer-copyright {
  margin-top: 48px;
  padding-top: 32px;
  border-top: 1px solid var(--ag-accent-300, #ddd6ca);
  text-align: center;
  font-family: var(--ag-font-body);
  font-size: 12px;
  color: var(--ag-accent-500, #a19f99);
}

/* ===== Animations ===== */
@keyframes scale-in {
  from { transform: scale(0); }
  to { transform: scale(1); }
}
.animate-scale-in { animation: scale-in 0.3s cubic-bezier(0.32, 0.72, 0, 1); }
</style>
