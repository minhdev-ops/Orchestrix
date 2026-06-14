import { reactive, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import api, { setApiToken, getApiToken } from '../services/api';

const state = reactive({
  user: JSON.parse(localStorage.getItem('user') || 'null'),
  token: getApiToken(),
  loading: false,
});

export function useAuth() {
  const isAuthenticated = computed(() => !!state.token || !!state.user);
  const user = computed(() => state.user);
  const role = computed(() => state.user?.role || null);

  const isBuyer = computed(() => role.value === 'buyer');
  const isSeller = computed(() => role.value === 'seller');
  const isAdmin = computed(() => role.value === 'admin');
  const isEmployee = computed(() => role.value === 'employee');

  function syncFromPageProps() {
    const page = usePage();
    const authUser = page.props.auth?.user;
    const pageToken = page.props.auth?.api_token;
    if (pageToken && !state.token) {
      setApiToken(pageToken);
      state.token = pageToken;
    }
    if (authUser) {
      state.user = authUser;
      localStorage.setItem('user', JSON.stringify(authUser));
    }
  }

  async function login(email, password) {
    state.loading = true;
    try {
      const { data } = await api.post('/login', { email, password });
      state.token = data.token;
      state.user = data.user;
      setApiToken(data.token);
      localStorage.setItem('user', JSON.stringify(data.user));
      redirectToDashboard(data.user.role);
      return data;
    } catch (err) {
      state.loading = false;
      throw err;
    }
  }

  async function register(formData) {
    state.loading = true;
    try {
      const { data } = await api.post('/register', formData);
      state.loading = false;
      return data;
    } catch (err) {
      state.loading = false;
      throw err;
    }
  }

  async function logout() {
    state.token = null;
    state.user = null;
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    router.post('/logout');
  }

  async function fetchUser() {
    try {
      const { data } = await api.get('/user/detail');
      state.user = data;
      localStorage.setItem('user', JSON.stringify(data));
      return data;
    } catch {
      return null;
    }
  }

  function redirectToDashboard(userRole) {
    const roleMap = {
      admin: '/admin/agriverse',
      seller: '/admin/agriverse',
      employee: '/admin/agriverse',
      buyer: '/agriverse',
    };
    const url = roleMap[userRole] || '/agriverse';
    window.location.href = url;
  }

  return {
    user,
    token: state.token,
    loading: state,
    isAuthenticated,
    role,
    isBuyer,
    isSeller,
    isAdmin,
    isEmployee,
    login,
    register,
    logout,
    fetchUser,
    syncFromPageProps,
    redirectToDashboard,
  };
}
