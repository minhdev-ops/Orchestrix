<template>
  <Head :title="pageTitle" />
  <div class="marketplace-page">
    <div class="page-header">
      <h1>{{ pageTitle }}</h1>
      <Link :href="route('agriverse.shop.addresses.create')" class="btn-add">+ Thêm địa chỉ</Link>
    </div>
    <div v-if="addresses.length === 0" class="empty-state">
      <Card>
        <template #content>
          <p>Bạn chưa có địa chỉ nào.</p>
          <Button label="Thêm địa chỉ mới" @click="addAddress" />
        </template>
      </Card>
    </div>
    <div v-else class="addresses-grid">
      <Card
        v-for="addr in addresses"
        :key="addr.id"
        class="address-card"
        :class="{ default: addr.is_default }"
      >
        <template #title>
          <div class="address-title">
            <span>{{ addr.label || 'Địa chỉ' }}</span>
            <span v-if="addr.is_default" class="default-badge">Mặc định</span>
          </div>
        </template>
        <template #content>
          <div class="address-detail">
            <p><strong>{{ addr.name }}</strong></p>
            <p>{{ addr.phone }}</p>
            <p>{{ addr.street_address }}</p>
            <p>{{ addr.ward_name }}, {{ addr.district_name }}, {{ addr.province_name }}</p>
          </div>
        </template>
        <template #footer>
          <div class="address-actions">
            <Link :href="route('agriverse.shop.addresses.edit', addr.id)" class="p-button p-button-sm p-button-outlined">
              Sửa
            </Link>
            <Button
              v-if="!addr.is_default"
              label="Đặt làm mặc định"
              size="small"
              severity="success"
              outlined
              @click="setDefault(addr.id)"
            />
            <Button label="Xoá" size="small" severity="danger" outlined @click="confirmDelete(addr.id)" />
          </div>
        </template>
      </Card>
    </div>
    <Dialog v-model:visible="deleteDialog" header="Xác nhận xoá" modal>
      <p>Bạn có chắc muốn xoá địa chỉ này?</p>
      <template #footer>
        <Button label="Huỷ" severity="secondary" @click="deleteDialog = false" />
        <Button label="Xoá" severity="danger" @click="deleteAddress" />
      </template>
    </Dialog>
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'

const props = defineProps({
  addresses: { type: Array, default: () => [] },
})

const deleteDialog = ref(false)
const deleteId = ref(null)

const pageTitle = computed(() => 'AgriVerse - Sổ địa chỉ')

function addAddress() {
  router.visit(route('agriverse.shop.addresses.create'))
}

function setDefault(id) {
  router.visit(route('agriverse.shop.addresses.set-default', id))
}

function confirmDelete(id) {
  deleteId.value = id
  deleteDialog.value = true
}

function deleteAddress() {
  router.delete(route('agriverse.shop.addresses.destroy', deleteId.value))
  deleteDialog.value = false
}
</script>

<style scoped>
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.btn-add {
  background: #22c55e;
  color: #fff;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  text-decoration: none;
  font-weight: 500;
}
.addresses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 1rem;
}
.address-card.default {
  border: 2px solid #22c55e;
}
.address-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.default-badge {
  font-size: 0.7rem;
  background: #dcfce7;
  color: #166534;
  padding: 0.15rem 0.5rem;
  border-radius: 999px;
  font-weight: 500;
}
.address-detail p {
  margin: 0.2rem 0;
  font-size: 0.9rem;
  color: #4b5563;
}
.address-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}
.empty-state {
  text-align: center;
}
</style>
