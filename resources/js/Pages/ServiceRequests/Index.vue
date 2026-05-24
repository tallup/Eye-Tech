<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/components/DataTable.vue'
import { Button } from '@/components/ui/button'
import { useAuth } from '@/composables/useAuth'

interface ServiceRef {
  id: number
  name: string
  price: number
}

interface ServiceRequestRow {
  id: number
  request_number: string
  customer_name: string
  customer_phone: string
  service: ServiceRef | null
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled'
  created_at: string
}

defineProps<{
  serviceRequests: { data: ServiceRequestRow[]; links: any[]; meta: any }
}>()

const { isAdmin } = useAuth()

function statusLabel(s: string) {
  switch (s) {
    case 'in_progress': return 'In Progress'
    case 'pending': return 'Pending'
    case 'completed': return 'Completed'
    case 'cancelled': return 'Cancelled'
    default: return s
  }
}

function statusClass(s: string) {
  switch (s) {
    case 'pending': return 'bg-amber-100 text-amber-800'
    case 'in_progress': return 'bg-blue-100 text-blue-800'
    case 'completed': return 'bg-green-100 text-green-800'
    case 'cancelled': return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}

function fmtDate(iso: string) {
  return new Date(iso).toLocaleDateString()
}

function view(row: ServiceRequestRow) {
  router.visit(`/app/service-requests/${row.id}`)
}

function goCreate() {
  router.visit('/app/service-requests/new')
}

const columns = [
  { key: 'request_number', label: 'Ref' },
  { key: 'customer_name', label: 'Customer' },
  { key: 'customer_phone', label: 'Phone' },
  { key: 'service', label: 'Service', render: (r: ServiceRequestRow) => r.service?.name ?? '—' },
  { key: 'status', label: 'Status' },
  { key: 'created_at', label: 'Filed', render: (r: ServiceRequestRow) => fmtDate(r.created_at) },
  { key: 'actions', label: '' },
]
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Service Requests</h1>
        <Button v-if="isAdmin" @click="goCreate">+ New request</Button>
      </div>

      <DataTable :data="serviceRequests" :columns="columns">
        <template #cell-status="{ row }">
          <span :class="['inline-block px-2 py-1 rounded text-xs font-medium', statusClass(row.status)]">
            {{ statusLabel(row.status) }}
          </span>
        </template>
        <template #cell-actions="{ row }">
          <Button variant="outline" size="sm" @click="view(row)">View</Button>
        </template>
      </DataTable>
    </div>
  </AppLayout>
</template>
