<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/components/DataTable.vue'
import type { Sale, SaleItem } from '@/types/models'

defineProps<{
  sales: {
    data: Sale[]
    links: any[]
    meta: any
  }
}>()

const columns = [
  { key: 'items_preview', label: 'Items' },
  { key: 'sale_number', label: 'Sale #' },
  { key: 'customer_name', label: 'Customer', render: (s: Sale) => s.customer_name ?? '—' },
  { key: 'total_amount', label: 'Total', render: (s: Sale) => Number(s.total_amount).toFixed(2) },
  { key: 'payment_method', label: 'Payment' },
  { key: 'status', label: 'Status' },
  { key: 'cashier', label: 'Cashier', render: (s: Sale) => s.user?.name ?? '—' },
  { key: 'created_at', label: 'Date', render: (s: Sale) => new Date(s.created_at).toLocaleString() },
]

function itemsForSale(s: Sale): (SaleItem & { product_image_url?: string | null })[] {
  const raw = (s as any).items
  if (!raw) return []
  if (Array.isArray(raw)) return raw
  if (Array.isArray(raw.data)) return raw.data
  return []
}
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h1 class="text-3xl font-bold">Sales</h1>
      <DataTable :data="sales" :columns="columns" :row-link="(s) => `/app/sales/${s.id}`">
        <template #cell-items_preview="{ row }">
          <div class="flex items-center -space-x-2">
            <div
              v-for="(item, i) in itemsForSale(row).slice(0, 4)"
              :key="item.id"
              :style="`z-index: ${10 - i}`"
              :title="`${item.item_name} × ${item.quantity}`"
              class="w-10 h-10 rounded-full ring-2 ring-background bg-muted overflow-hidden flex items-center justify-center"
            >
              <img
                v-if="item.product_image_url"
                :src="item.product_image_url"
                :alt="item.item_name"
                class="w-full h-full object-cover"
                loading="lazy"
                @error="($event.target as HTMLImageElement).style.display = 'none'"
              />
              <span v-else class="text-[8px] text-muted-foreground px-1 truncate">{{ item.item_name.slice(0, 3) }}</span>
            </div>
            <div
              v-if="itemsForSale(row).length > 4"
              class="w-10 h-10 rounded-full ring-2 ring-background bg-muted flex items-center justify-center text-xs font-medium text-muted-foreground"
            >
              +{{ itemsForSale(row).length - 4 }}
            </div>
            <span v-if="itemsForSale(row).length === 0" class="text-xs text-muted-foreground">—</span>
          </div>
        </template>
      </DataTable>
    </div>
  </AppLayout>
</template>
