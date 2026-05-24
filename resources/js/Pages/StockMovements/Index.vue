<script setup lang="ts">
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/components/DataTable.vue'
import { Button } from '@/components/ui/button'

interface StockMovement {
  id: number
  product: { id: number; name: string; sku: string } | null
  movement_type: string
  quantity: number
  previous_quantity: number
  new_quantity: number
  reference_type: string | null
  reference_id: number | null
  user: { id: number; name: string } | null
  notes: string | null
  created_at: string
}

const props = defineProps<{
  movements: { data: StockMovement[]; links: any[]; meta: any }
  filters: { type?: string }
}>()

const selectedType = ref<string | null>(props.filters.type ?? null)

watch(selectedType, (val) => {
  router.get('/app/stock-movements', { type: val || undefined }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
})

const types = ['in', 'out', 'sale', 'adjustment', 'transfer', 'purchase']

function typeBadge(t: string): string {
  const map: Record<string, string> = {
    in: 'bg-green-100 text-green-800',
    out: 'bg-orange-100 text-orange-800',
    sale: 'bg-blue-100 text-blue-800',
    adjustment: 'bg-yellow-100 text-yellow-800',
    transfer: 'bg-purple-100 text-purple-800',
    purchase: 'bg-cyan-100 text-cyan-800',
  }
  return map[t] ?? 'bg-muted text-foreground'
}

const columns = [
  { key: 'created_at', label: 'Date', render: (m: StockMovement) => new Date(m.created_at).toLocaleString() },
  { key: 'product', label: 'Product', render: (m: StockMovement) => m.product ? `${m.product.name} (${m.product.sku})` : '—' },
  { key: 'movement_type', label: 'Type' },
  { key: 'quantity', label: 'Qty' },
  { key: 'change', label: 'Change', render: (m: StockMovement) => `${m.previous_quantity} → ${m.new_quantity}` },
  { key: 'user', label: 'By', render: (m: StockMovement) => m.user?.name ?? '—' },
  { key: 'notes', label: 'Notes', render: (m: StockMovement) => m.notes ?? '—' },
]
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h1 class="text-3xl font-bold">Stock movements</h1>

      <div class="flex gap-2 flex-wrap">
        <Button :variant="selectedType === null ? 'default' : 'outline'" size="sm" @click="selectedType = null">All</Button>
        <Button v-for="t in types" :key="t" :variant="selectedType === t ? 'default' : 'outline'" size="sm" @click="selectedType = t">{{ t }}</Button>
      </div>

      <DataTable :data="movements" :columns="columns">
        <template #cell-movement_type="{ row }">
          <span :class="['px-2 py-0.5 rounded text-xs font-medium', typeBadge(row.movement_type)]">
            {{ row.movement_type }}
          </span>
        </template>
      </DataTable>
    </div>
  </AppLayout>
</template>
