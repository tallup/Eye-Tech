<script setup lang="ts">
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/components/DataTable.vue'
import FormDialog from '@/components/FormDialog.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

interface Service {
  id: number
  name: string
  slug: string
  description: string
  price: number
  estimated_duration: number | null
  category: string | null
  is_active: boolean
  is_featured: boolean
  created_at: string
}

defineProps<{
  services: { data: Service[]; links: any[]; meta: any }
}>()

const dialogOpen = ref(false)
const editing = ref<Service | null>(null)

const form = useForm({
  name: '',
  description: '',
  price: 0,
  estimated_duration: undefined as number | undefined,
  category: '',
  is_active: true,
  is_featured: false,
})

function openCreate() {
  editing.value = null
  form.reset()
  dialogOpen.value = true
}

function openEdit(service: Service) {
  editing.value = service
  form.name = service.name
  form.description = service.description
  form.price = service.price
  form.estimated_duration = service.estimated_duration ?? undefined
  form.category = service.category ?? ''
  form.is_active = service.is_active
  form.is_featured = service.is_featured
  dialogOpen.value = true
}

function submit() {
  const onSuccess = () => {
    dialogOpen.value = false
    toast.success(editing.value ? 'Service updated' : 'Service created')
  }
  if (editing.value) {
    form.put(`/app/services/${editing.value.id}`, { onSuccess, preserveScroll: true })
  } else {
    form.post('/app/services', { onSuccess, preserveScroll: true })
  }
}

function destroy(service: Service) {
  if (!confirm(`Delete ${service.name}?`)) return
  router.delete(`/app/services/${service.id}`, {
    preserveScroll: true,
    onSuccess: () => toast.success('Service deleted'),
  })
}

function fmtPrice(v: number) {
  return new Intl.NumberFormat('en-GM', { style: 'currency', currency: 'GMD' }).format(v)
}

const columns = [
  { key: 'name', label: 'Name' },
  { key: 'category', label: 'Category', render: (s: Service) => s.category ?? '—' },
  { key: 'price', label: 'Price', render: (s: Service) => fmtPrice(s.price) },
  { key: 'estimated_duration', label: 'Duration', render: (s: Service) => s.estimated_duration ? `${s.estimated_duration} min` : '—' },
  { key: 'is_featured', label: 'Featured', render: (s: Service) => s.is_featured ? 'Yes' : '—' },
  { key: 'is_active', label: 'Active', render: (s: Service) => s.is_active ? 'Yes' : 'No' },
  { key: 'actions', label: '' },
]
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Services</h1>
        <Button @click="openCreate">+ New service</Button>
      </div>

      <DataTable :data="services" :columns="columns">
        <template #cell-actions="{ row }">
          <div class="flex gap-2 justify-end">
            <Button variant="outline" size="sm" @click="openEdit(row)">Edit</Button>
            <Button variant="destructive" size="sm" @click="destroy(row)">Delete</Button>
          </div>
        </template>
      </DataTable>

      <FormDialog
        :open="dialogOpen"
        :title="editing ? 'Edit service' : 'New service'"
        :processing="form.processing"
        @update:open="dialogOpen = $event"
        @submit="submit"
      >
        <div class="space-y-2">
          <Label html-for="name">Name</Label>
          <Input id="name" v-model="form.name" />
          <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
        </div>
        <div class="space-y-2">
          <Label html-for="description">Description</Label>
          <textarea id="description" v-model="form.description" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" rows="3"></textarea>
          <p v-if="form.errors.description" class="text-sm text-destructive">{{ form.errors.description }}</p>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div class="space-y-2">
            <Label html-for="price">Price (GMD)</Label>
            <Input id="price" type="number" step="0.01" v-model.number="form.price" />
            <p v-if="form.errors.price" class="text-sm text-destructive">{{ form.errors.price }}</p>
          </div>
          <div class="space-y-2">
            <Label html-for="estimated_duration">Duration (minutes)</Label>
            <Input id="estimated_duration" type="number" v-model.number="form.estimated_duration" />
          </div>
        </div>
        <div class="space-y-2">
          <Label html-for="category">Category</Label>
          <Input id="category" v-model="form.category" placeholder="e.g. repair, unlock, install" />
        </div>
        <div class="flex items-center gap-6 pt-2">
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" v-model="form.is_active" />
            Active
          </label>
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" v-model="form.is_featured" />
            Featured
          </label>
        </div>
      </FormDialog>
    </div>
  </AppLayout>
</template>
