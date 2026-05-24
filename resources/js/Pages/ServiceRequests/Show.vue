<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { useAuth } from '@/composables/useAuth'

interface ServiceRef { id: number; name: string; price: number }

interface ServiceRequest {
  id: number
  request_number: string
  customer_name: string
  customer_phone: string
  customer_email: string | null
  service_id: number
  service: ServiceRef | null
  device_description: string
  problem_description: string
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled'
  estimated_cost: number | null
  final_cost: number | null
  notes: string | null
  completed_at: string | null
  created_at: string
}

const props = defineProps<{ serviceRequest: ServiceRequest }>()
const { isAdmin } = useAuth()

function setStatus(next: 'pending' | 'in_progress' | 'completed' | 'cancelled') {
  const payload = {
    customer_name: props.serviceRequest.customer_name,
    customer_phone: props.serviceRequest.customer_phone,
    customer_email: props.serviceRequest.customer_email,
    service_id: props.serviceRequest.service_id,
    device_description: props.serviceRequest.device_description,
    problem_description: props.serviceRequest.problem_description,
    status: next,
    estimated_cost: props.serviceRequest.estimated_cost,
    final_cost: props.serviceRequest.final_cost,
    notes: props.serviceRequest.notes,
  }
  router.put(`/app/service-requests/${props.serviceRequest.id}`, payload, {
    onSuccess: () => toast.success(`Marked ${next.replace('_', ' ')}`),
    preserveScroll: true,
  })
}

function destroy() {
  if (!confirm('Delete this service request?')) return
  router.delete(`/app/service-requests/${props.serviceRequest.id}`, {
    onSuccess: () => toast.success('Deleted'),
  })
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

function fmt(v: number | null) {
  if (v === null) return '—'
  return new Intl.NumberFormat('en-GM', { style: 'currency', currency: 'GMD' }).format(v)
}
</script>

<template>
  <AppLayout>
    <div class="space-y-6 max-w-3xl">
      <div class="flex justify-between items-start">
        <div>
          <h1 class="text-3xl font-bold">{{ serviceRequest.request_number }}</h1>
          <p class="text-sm text-muted-foreground mt-1">
            Filed {{ new Date(serviceRequest.created_at).toLocaleString() }}
          </p>
        </div>
        <span :class="['inline-block px-3 py-1 rounded text-sm font-medium', statusClass(serviceRequest.status)]">
          {{ serviceRequest.status.replace('_', ' ') }}
        </span>
      </div>

      <div class="rounded-lg border bg-card p-6 space-y-3">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p class="text-xs text-muted-foreground">Customer</p>
            <p class="font-medium">{{ serviceRequest.customer_name }}</p>
          </div>
          <div>
            <p class="text-xs text-muted-foreground">Phone</p>
            <p class="font-medium">{{ serviceRequest.customer_phone }}</p>
          </div>
          <div v-if="serviceRequest.customer_email">
            <p class="text-xs text-muted-foreground">Email</p>
            <p class="font-medium">{{ serviceRequest.customer_email }}</p>
          </div>
          <div v-if="serviceRequest.service">
            <p class="text-xs text-muted-foreground">Service</p>
            <p class="font-medium">{{ serviceRequest.service.name }}</p>
          </div>
        </div>
        <div>
          <p class="text-xs text-muted-foreground">Device</p>
          <p>{{ serviceRequest.device_description }}</p>
        </div>
        <div>
          <p class="text-xs text-muted-foreground">Problem</p>
          <p class="whitespace-pre-wrap">{{ serviceRequest.problem_description }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p class="text-xs text-muted-foreground">Estimated cost</p>
            <p class="font-medium">{{ fmt(serviceRequest.estimated_cost) }}</p>
          </div>
          <div>
            <p class="text-xs text-muted-foreground">Final cost</p>
            <p class="font-medium">{{ fmt(serviceRequest.final_cost) }}</p>
          </div>
        </div>
        <div v-if="serviceRequest.notes">
          <p class="text-xs text-muted-foreground">Notes</p>
          <p class="whitespace-pre-wrap">{{ serviceRequest.notes }}</p>
        </div>
        <div v-if="serviceRequest.completed_at">
          <p class="text-xs text-muted-foreground">Completed at</p>
          <p>{{ new Date(serviceRequest.completed_at).toLocaleString() }}</p>
        </div>
      </div>

      <div v-if="isAdmin" class="flex flex-wrap gap-2">
        <Button
          v-if="serviceRequest.status !== 'in_progress'"
          variant="outline"
          @click="setStatus('in_progress')"
        >Mark in progress</Button>
        <Button
          v-if="serviceRequest.status !== 'completed'"
          @click="setStatus('completed')"
        >Mark completed</Button>
        <Button
          v-if="serviceRequest.status !== 'cancelled'"
          variant="outline"
          @click="setStatus('cancelled')"
        >Cancel</Button>
        <Button variant="destructive" @click="destroy">Delete</Button>
      </div>
    </div>
  </AppLayout>
</template>
