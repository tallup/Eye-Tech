<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/components/DataTable.vue'
import type { Sale } from '@/types/models'

defineProps<{
  sales: {
    data: Sale[]
    links: any[]
    meta: any
  }
}>()

const columns = [
  { key: 'sale_number', label: 'Sale #' },
  { key: 'customer_name', label: 'Customer', render: (s: Sale) => s.customer_name ?? '—' },
  { key: 'total_amount', label: 'Total', render: (s: Sale) => s.total_amount.toFixed(2) },
  { key: 'payment_method', label: 'Payment' },
  { key: 'status', label: 'Status' },
  { key: 'cashier', label: 'Cashier', render: (s: Sale) => s.user?.name ?? '—' },
  { key: 'created_at', label: 'Date', render: (s: Sale) => new Date(s.created_at).toLocaleString() },
]
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h1 class="text-3xl font-bold">Sales</h1>
      <DataTable :data="sales" :columns="columns" :row-link="(s) => `/app/sales/${s.id}`" />
    </div>
  </AppLayout>
</template>
