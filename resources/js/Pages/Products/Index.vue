<script setup lang="ts">
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/components/DataTable.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import type { Product } from '@/types/models'

const props = defineProps<{
  products: { data: Product[]; links: any[]; meta: any }
  categories: { id: number; name: string }[]
  filters: { search?: string; category_id?: number }
}>()

const search = ref(props.filters.search ?? '')
const categoryId = ref<number | null>(props.filters.category_id ?? null)

let timer: number | undefined
watch([search, categoryId], () => {
  clearTimeout(timer)
  timer = window.setTimeout(() => {
    router.get('/app/products', {
      search: search.value || undefined,
      category_id: categoryId.value || undefined,
    }, { preserveState: true, preserveScroll: true, replace: true })
  }, 250)
})

function destroy(product: Product) {
  if (!confirm(`Delete ${product.name}?`)) return
  router.delete(`/app/products/${product.id}`, {
    preserveScroll: true,
    onSuccess: () => toast.success('Product deleted'),
  })
}

const columns = [
  { key: 'image', label: '' },
  { key: 'name', label: 'Name' },
  { key: 'sku', label: 'SKU' },
  { key: 'category', label: 'Category', render: (p: Product) => p.category?.name ?? '—' },
  { key: 'selling_price', label: 'Price', render: (p: Product) => p.selling_price.toFixed(2) },
  { key: 'stock', label: 'Stock' },
  { key: 'actions', label: '' },
]
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Products</h1>
        <Link href="/app/products/create">
          <Button>+ New product</Button>
        </Link>
      </div>

      <div class="flex gap-2">
        <Input v-model="search" placeholder="Search name or SKU" class="max-w-md" />
        <select v-model="categoryId" class="rounded-md border border-input bg-background h-10 px-3 text-sm">
          <option :value="null">All categories</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
      </div>

      <DataTable :data="products" :columns="columns">
        <template #cell-image="{ row }">
          <div class="w-12 h-12 bg-muted rounded overflow-hidden flex items-center justify-center">
            <img v-if="row.image_url" :src="row.image_url" :alt="row.name" class="w-full h-full object-cover" />
            <span v-else class="text-xs text-muted-foreground">—</span>
          </div>
        </template>
        <template #cell-stock="{ row }">
          <span :class="row.stock_quantity <= row.min_stock_level ? 'text-destructive font-semibold' : ''">
            {{ row.stock_quantity }}
          </span>
        </template>
        <template #cell-actions="{ row }">
          <div class="flex gap-2 justify-end">
            <Link :href="`/app/products/${row.id}/edit`">
              <Button variant="outline" size="sm">Edit</Button>
            </Link>
            <Button variant="destructive" size="sm" @click="destroy(row)">Delete</Button>
          </div>
        </template>
      </DataTable>
    </div>
  </AppLayout>
</template>
