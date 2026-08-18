import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Auto-send CSRF token from meta tag for POST/PUT/DELETE requests
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}
