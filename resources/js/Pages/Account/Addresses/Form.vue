<template>
  <Head :title="pageTitle" />
  <div class="marketplace-page">
    <div class="page-header">
      <h1>{{ pageTitle }}</h1>
      <Link :href="route('agriverse.shop.addresses.index')" class="back-link">← Quay lại sổ địa chỉ</Link>
    </div>
    <Card>
      <template #content>
        <form @submit.prevent="submit" class="address-form">
          <div class="form-grid">
            <div class="field">
              <label for="name">Họ và tên người nhận</label>
              <InputText id="name" v-model="form.name" class="w-full" :class="{ 'p-invalid': form.errors.name }" />
              <small v-if="form.errors.name" class="error">{{ form.errors.name }}</small>
            </div>
            <div class="field">
              <label for="phone">Số điện thoại</label>
              <InputText id="phone" v-model="form.phone" class="w-full" :class="{ 'p-invalid': form.errors.phone }" />
              <small v-if="form.errors.phone" class="error">{{ form.errors.phone }}</small>
            </div>
            <div class="field">
              <label for="label">Tên gợi nhớ</label>
              <InputText id="label" v-model="form.label" class="w-full" placeholder="VD: Nhà riêng, Văn phòng" />
            </div>
            <div class="field">
              <label for="province">Tỉnh/Thành phố</label>
              <select id="province" v-model="form.province_id" class="w-full p-inputtext" @change="onProvinceChange">
                <option value="">Chọn tỉnh/thành</option>
                <option v-for="p in provinces" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
              <small v-if="form.errors.province_id" class="error">{{ form.errors.province_id }}</small>
            </div>
            <div class="field">
              <label for="district">Quận/Huyện</label>
              <select id="district" v-model="form.district_id" class="w-full p-inputtext" @change="onDistrictChange">
                <option value="">Chọn quận/huyện</option>
                <option v-for="d in districts" :key="d.id" :value="d.id">{{ d.name }}</option>
              </select>
              <small v-if="form.errors.district_id" class="error">{{ form.errors.district_id }}</small>
            </div>
            <div class="field">
              <label for="ward">Phường/Xã</label>
              <select id="ward" v-model="form.ward_id" class="w-full p-inputtext">
                <option value="">Chọn phường/xã</option>
                <option v-for="w in wards" :key="w.id" :value="w.id">{{ w.name }}</option>
              </select>
              <small v-if="form.errors.ward_id" class="error">{{ form.errors.ward_id }}</small>
            </div>
            <div class="field full-width">
              <label for="street">Địa chỉ cụ thể (số nhà, đường)</label>
              <InputText id="street" v-model="form.street_address" class="w-full" />
              <small v-if="form.errors.street_address" class="error">{{ form.errors.street_address }}</small>
            </div>
            <div class="field full-width">
              <label class="checkbox-label">
                <input type="checkbox" v-model="form.is_default" />
                Đặt làm địa chỉ mặc định
              </label>
            </div>
          </div>
          <div class="form-actions">
            <Link :href="route('agriverse.shop.addresses.index')" class="p-button p-button-secondary">Huỷ</Link>
            <Button type="submit" :label="address ? 'Cập nhật' : 'Thêm địa chỉ'" />
          </div>
        </form>
      </template>
    </Card>
  </div>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import Card from 'primevue/card'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'

const props = defineProps({
  address: { type: Object, default: null },
  provinces: { type: Array, default: () => [] },
  districts: { type: Array, default: () => [] },
  wards: { type: Array, default: () => [] },
})

const pageTitle = computed(() => props.address ? 'AgriVerse - Chỉnh sửa địa chỉ' : 'AgriVerse - Thêm địa chỉ')

const form = useForm({
  name: props.address?.name || '',
  phone: props.address?.phone || '',
  label: props.address?.label || '',
  province_id: props.address?.province_id || '',
  district_id: props.address?.district_id || '',
  ward_id: props.address?.ward_id || '',
  street_address: props.address?.street_address || '',
  is_default: props.address?.is_default || false,
})

function onProvinceChange() {
  form.district_id = ''
  form.ward_id = ''
  router.visit(route('agriverse.shop.addresses.form', { province_id: form.province_id }))
}

function onDistrictChange() {
  form.ward_id = ''
  router.visit(route('agriverse.shop.addresses.form', { province_id: form.province_id, district_id: form.district_id }))
}

function submit() {
  if (props.address) {
    form.put(route('agriverse.shop.addresses.update', props.address.id), { preserveScroll: true })
  } else {
    form.post(route('agriverse.shop.addresses.store'), { preserveScroll: true })
  }
}
</script>

<style scoped>
.back-link {
  color: #22c55e;
  text-decoration: none;
  font-size: 0.9rem;
  display: inline-block;
  margin-bottom: 1rem;
}
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
.field.full-width {
  grid-column: 1 / -1;
}
.field label {
  display: block;
  font-weight: 500;
  margin-bottom: 0.375rem;
  font-size: 0.9rem;
}
.w-full {
  width: 100%;
}
.error {
  color: #ef4444;
  font-size: 0.8rem;
}
.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-weight: 400;
}
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.5rem;
}
</style>
