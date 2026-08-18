<template>
  <div class="login-page">
    <div class="login-container">
      <div class="login-card">
        <Link :href="route('agriverse.shop.home')" class="login-logo">
          <div class="login-logo-mark">
            <span class="material-symbols-outlined text-white" style="font-size: 20px;">eco</span>
          </div>
          <span class="login-logo-text">AgriVerse</span>
        </Link>

        <h1 class="login-title">Đăng nhập</h1>
        <p class="login-subtitle">Chào mừng trở lại AgriVerse</p>

        <form @submit.prevent="handleLogin" class="login-form">
          <div v-if="errorMsg" class="login-error">{{ errorMsg }}</div>

          <div>
            <label class="login-label">Email</label>
            <input v-model="form.email" type="email" required
              class="login-input"
              placeholder="your@email.com" />
          </div>

          <div>
            <label class="login-label">Mật khẩu</label>
            <input v-model="form.password" type="password" required
              class="login-input"
              placeholder="••••••••" />
          </div>

          <!-- CAPTCHA (v2 invisible — rendered programmatically) -->
          <div v-if="captchaSiteKey" ref="captchaContainer" class="login-captcha"></div>

          <div class="login-options">
            <label class="login-remember">
              <input type="checkbox" class="login-checkbox" />
              <span>Ghi nhớ</span>
            </label>
            <Link href="/password/reset" class="login-forgot">Quên mật khẩu?</Link>
          </div>

          <button type="submit" :disabled="loading" class="login-submit">
            <span v-if="loading" class="login-spinner" />
            {{ loading ? 'Đang đăng nhập...' : 'Đăng nhập' }}
          </button>
        </form>

        <div class="login-divider">
          <span>hoặc tiếp tục với</span>
        </div>

        <div class="login-social">
          <button @click="loginWithGoogle" :disabled="socialLoading" class="login-social-btn">
            <svg class="login-social-icon" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Google
          </button>
          <button @click="loginWithFacebook" :disabled="socialLoading" class="login-social-btn login-social-fb">
            <svg class="login-social-icon" viewBox="0 0 24 24" fill="#1877F2">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.284H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874V21h3.328l-.532 3.47h-2.796v8.284C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Facebook
          </button>
        </div>

        <div class="login-divider">
          <span>Chưa có tài khoản?</span>
          <Link :href="route('register')" class="login-register-link">Đăng ký</Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const loading = ref(false);
const socialLoading = ref(false);
const page = usePage();
const captchaContainer = ref(null);

const captchaSiteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY || '';

const errorMsg = computed(() => {
  const errors = page.props.errors || {};
  return Object.values(errors).flat().join(', ');
});

const form = reactive({
  email: '',
  password: '',
});

let recaptchaWidgetId = null;

onMounted(() => {
  if (!captchaSiteKey) return;
  if (window.grecaptcha) {
    renderCaptcha();
    return;
  }
  window._recaptchaOnload = renderCaptcha;
  const script = document.createElement('script');
  script.src = 'https://www.google.com/recaptcha/api.js?onload=_recaptchaOnload&render=explicit';
  script.async = true;
  document.head.appendChild(script);
});

function renderCaptcha() {
  if (!window.grecaptcha || !captchaContainer.value) return;
  recaptchaWidgetId = window.grecaptcha.render(captchaContainer.value, {
    sitekey: captchaSiteKey,
  });
}

onBeforeUnmount(() => {
  if (window.grecaptcha && recaptchaWidgetId !== null) {
    window.grecaptcha.reset(recaptchaWidgetId);
  }
});

function getCaptchaToken() {
  if (!captchaSiteKey || !window.grecaptcha || recaptchaWidgetId === null) return Promise.resolve(null);
  const response = window.grecaptcha.getResponse(recaptchaWidgetId);
  return Promise.resolve(response);
}

async function handleLogin() {
  loading.value = true;
  try {
    const captchaToken = await getCaptchaToken();
    const payload = { ...form };
    if (captchaToken) payload.captcha_token = captchaToken;
    router.post('/login', payload, {
      preserveState: true,
      onFinish: () => {
        loading.value = false;
      },
    });
  } catch {
    loading.value = false;
  }
}

function loginWithGoogle() {
  socialLoading.value = true;
  if (window.google && window.google.accounts && window.google.accounts.id) {
    window.google.accounts.id.initialize({
      client_id: import.meta.env.VITE_GOOGLE_CLIENT_ID || '',
      callback: handleGoogleCredential,
    });
    window.google.accounts.id.prompt();
  } else {
    window.location.href = 'https://accounts.google.com/o/oauth2/v2/auth?client_id='
      + (import.meta.env.VITE_GOOGLE_CLIENT_ID || '')
      + '&redirect_uri=' + encodeURIComponent(window.location.origin + '/auth/google/callback')
      + '&response_type=code&scope=email profile';
  }
}

function handleGoogleCredential(response) {
  router.post('/auth/google', { credential: response.credential }, {
    onFinish: () => { socialLoading.value = false; },
  });
}

function initFacebookSDK() {
  return new Promise((resolve) => {
    if (window.FB) {
      resolve();
      return;
    }
    const fbAppId = import.meta.env.VITE_FACEBOOK_APP_ID || '';
    if (!fbAppId) {
      resolve();
      return;
    }
    window.fbAsyncInit = function() {
      window.FB.init({
        appId: fbAppId,
        cookie: true,
        xfbml: true,
        version: 'v18.0',
      });
      resolve();
    };
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "https://connect.facebook.net/vi_VN/sdk.js";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  });
}

function loginWithFacebook() {
  socialLoading.value = true;
  initFacebookSDK().then(() => {
    if (!window.FB) {
      socialLoading.value = false;
      return;
    }
    window.FB.login(function(response) {
      if (response.authResponse) {
        const accessToken = response.authResponse.accessToken;
        const userID = response.authResponse.userID;

        window.FB.api('/me', { fields: 'name,email,picture' }, function(profile) {
          router.post('/auth/facebook', {
            access_token: accessToken,
            user_id: userID,
            name: profile.name,
            email: profile.email || '',
            picture: profile.picture?.data?.url || '',
          }, {
            onFinish: () => { socialLoading.value = false; },
          });
        });
      } else {
        socialLoading.value = false;
      }
    }, { scope: 'public_profile,email' });
  });
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--ag-bg);
}
.login-container {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px 20px;
}
.login-card {
  width: 100%;
  max-width: 420px;
  background: var(--ag-bg-card);
  border-radius: var(--ag-radius-2xl);
  border: 1px solid var(--ag-border);
  padding: 40px;
}
.login-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-bottom: 40px;
  text-decoration: none;
}
.login-logo-mark {
  width: 40px;
  height: 40px;
  border-radius: var(--ag-radius-lg);
  background: var(--ag-primary-500);
  display: flex;
  align-items: center;
  justify-content: center;
}
.login-logo-text {
  font-family: var(--ag-font-display);
  font-size: 22px;
  font-weight: 500;
  color: var(--ag-primary-500);
  font-style: italic;
  letter-spacing: -0.02em;
}
.login-title {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  color: var(--ag-on-surface);
  text-align: center;
  margin-bottom: 4px;
}
.login-subtitle {
  font-family: var(--ag-font-body);
  font-size: 14px;
  color: var(--ag-on-surface-variant);
  text-align: center;
  margin-bottom: 32px;
}
.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.login-error {
  padding: 12px 16px;
  border-radius: var(--ag-radius-lg);
  background: color-mix(in srgb, var(--ag-error) 8%, transparent);
  color: var(--ag-error);
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 500;
  line-height: 1.4;
}
.login-label {
  display: block;
  font-family: var(--ag-font-body);
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--ag-on-surface-variant);
  margin-bottom: 6px;
}
.login-input {
  width: 100%;
  height: 48px;
  padding: 0 16px;
  border-radius: var(--ag-radius-xl);
  border: 2px solid var(--ag-border);
  font-family: var(--ag-font-body);
  font-size: 14px;
  color: var(--ag-on-surface);
  background: var(--ag-bg-card);
  outline: none;
  transition: var(--ag-transition-base);
  box-sizing: border-box;
}
.login-input:hover { border-color: var(--ag-outline-variant); }
.login-input:focus {
  border-color: var(--ag-primary);
  box-shadow: 0 0 0 4px color-mix(in srgb, var(--ag-primary) 10%, transparent);
}
.login-input::placeholder { color: var(--ag-border); }
.login-captcha {
  display: flex;
  justify-content: center;
}
.login-options {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-family: var(--ag-font-body);
  font-size: 13px;
}
.login-remember {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  color: var(--ag-on-surface-variant);
}
.login-checkbox {
  width: 16px;
  height: 16px;
  accent-color: var(--ag-primary-500);
  border-radius: 4px;
}
.login-forgot {
  color: var(--ag-primary);
  font-weight: 500;
  text-decoration: none;
  transition: opacity 0.2s;
}
.login-forgot:hover { opacity: 0.7; }
.login-submit {
  width: 100%;
  height: 48px;
  border-radius: var(--ag-radius-2xl);
  background: var(--ag-primary-500);
  color: white;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: var(--ag-transition-base);
}
.login-submit:hover {
  background: var(--ag-primary-600);
  box-shadow: 0 4px 14px -2px color-mix(in srgb, var(--ag-primary-500) 30%, transparent);
}
.login-submit:active { transform: scale(0.97); }
.login-submit:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
.login-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.login-divider {
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid var(--ag-border);
  text-align: center;
  font-family: var(--ag-font-body);
  font-size: 13px;
  color: var(--ag-on-surface-variant);
}
.login-register-link {
  color: var(--ag-primary);
  font-weight: 600;
  text-decoration: none;
  margin-left: 4px;
}
.login-register-link:hover { opacity: 0.7; }
.login-social {
  display: flex;
  gap: 12px;
  margin-top: 16px;
}
.login-social-btn {
  flex: 1;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border: 1px solid var(--ag-border);
  border-radius: var(--ag-radius-xl);
  background: white;
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 500;
  color: var(--ag-text-primary);
  cursor: pointer;
  transition: all 0.2s;
}
.login-social-btn:hover {
  border-color: var(--ag-outline-variant);
  background: var(--ag-surface-container-low);
}
.login-social-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.login-social-icon { width: 18px; height: 18px; }
.login-demo {
  margin-top: 20px;
  padding: 16px;
  border-radius: var(--ag-radius-xl);
  background: var(--ag-surface-container-low);
  border: 1px solid var(--ag-border);
}
.login-demo-title {
  font-family: var(--ag-font-body);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--ag-on-surface-variant);
  margin-bottom: 8px;
}
.login-demo-list {
  font-family: var(--ag-font-body);
  font-size: 12px;
  color: var(--ag-on-surface-variant);
  line-height: 1.8;
}
.login-demo-pw {
  margin-top: 4px;
  color: var(--ag-text-muted);
}
.login-demo-pw span { font-family: monospace; }
</style>
