export function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price);
}

export const statusLabels = {
  pending: 'Chờ xác nhận', confirmed: 'Đã xác nhận', shipping: 'Đang giao',
  delivered: 'Đã giao', completed: 'Hoàn thành', cancelled: 'Đã hủy', refunded: 'Đã hoàn tiền',
  awaiting_payment: 'Chờ thanh toán',
  published: 'Đã đăng', draft: 'Nháp', archived: 'Lưu trữ',
  active: 'Hoạt động', inactive: 'Tạm ngưng', suspended: 'Khóa',
  paid: 'Đã TT', failed: 'Thất bại',
  processing: 'Đang xử lý',
  percent: '%', fixed: 'VNĐ',
};

export const statusClasses = {
  pending: 'bg-amber-50 text-amber-600', confirmed: 'bg-blue-50 text-blue-600',
  shipping: 'bg-sky-50 text-sky-600', delivered: 'bg-indigo-50 text-indigo-600',
  completed: 'bg-emerald-50 text-emerald-600', cancelled: 'bg-red-50 text-red-600',
  refunded: 'bg-purple-50 text-purple-600', awaiting_payment: 'bg-orange-50 text-orange-600',
  approved: 'bg-emerald-50 text-emerald-600', rejected: 'bg-red-50 text-red-600',
  published: 'bg-emerald-50 text-emerald-600',
  draft: 'bg-amber-50 text-amber-600', archived: 'bg-stone-100 text-stone-500',
  active: 'bg-emerald-50 text-emerald-600', inactive: 'bg-amber-50 text-amber-600',
  suspended: 'bg-red-50 text-red-600', paid: 'bg-emerald-50 text-emerald-600',
  failed: 'bg-red-50 text-red-600', processing: 'bg-blue-50 text-blue-600',
};

export function statusLabel(status) {
  return statusLabels[status] || status;
}

export function statusClass(status) {
  return statusClasses[status] || 'bg-stone-100 text-stone-600';
}
