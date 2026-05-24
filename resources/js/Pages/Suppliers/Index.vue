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

interface Supplier {
  id: number
  name: string
  email: string | null
  phone: string | null
  address: string | null
  contact_person: string | null
  notes: string | null
  is_active: boolean
  created_at: string
}

defineProps<{
  suppliers: { data: Supplier[]; links: any[]; meta: any }
}>()

const dialogOpen = ref(false)
const editing = ref<Supplier | null>(null)

const form = useForm({
  name: '',
  email: '',
  phone: '',
  address: '',
  contact_person: '',
  notes: '',
})

function openCreate() {
  editing.value = null
  form.reset()
  dialogOpen.value = true
}

function openEdit(supplier: Supplier) {
  editing.value = supplier
  form.name = supplier.name
  form.email = supplier.email ?? ''
  form.phone = supplier.phone ?? ''
  form.address = supplier.address ?? ''
  form.contact_person = supplier.contact_person ?? ''
  form.notes = supplier.notes ?? ''
  dialogOpen.value = true
}

function submit() {
  const onSuccess = () => {
    dialogOpen.value = false
    toast.success(editing.value ? 'Supplier updated' : 'Supplier created')
  }
  if (editing.value) {
    form.put(`/app/suppliers/${editing.value.id}`, { onSuccess, preserveScroll: true })
  } else {
    form.post('/app/suppliers', { onSuccess, preserveScroll: true })
  }
}

function destroy(supplier: Supplier) {
  if (!confirm(`Delete ${supplier.name}?`)) return
  router.delete(`/app/suppliers/${supplier.id}`, {
    preserveScroll: true,
    onSuccess: () => toast.success('Supplier deleted'),
  })
}

const columns = [
  { key: 'name', label: 'Name' },
  { key: 'email', label: 'Email', render: (s: Supplier) => s.email ?? '—' },
  { key: 'phone', label: 'Phone', render: (s: Supplier) => s.phone ?? '—' },
  { key: 'contact_person', label: 'Contact', render: (s: Supplier) => s.contact_person ?? '—' },
  { key: 'actions', label: '' },
]
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Suppliers</h1>
        <Button @click="openCreate">+ New supplier</Button>
      </div>

      <DataTable :data="suppliers" :columns="columns">
        <template #cell-actions="{ row }">
          <div class="flex gap-2 justify-end">
            <Button variant="outline" size="sm" @click="openEdit(row)">Edit</Button>
            <Button variant="destructive" size="sm" @click="destroy(row)">Delete</Button>
          </div>
        </template>
      </DataTable>

      <FormDialog
        :open="dialogOpen"
        :title="editing ? 'Edit supplier' : 'New supplier'"
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
          <Label html-for="email">Email</Label>
          <Input id="email" type="email" v-model="form.email" />
          <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
        </div>
        <div class="space-y-2">
          <Label html-for="phone">Phone</Label>
          <Input id="phone" v-model="form.phone" />
          <p v-if="form.errors.phone" class="text-sm text-destructive">{{ form.errors.phone }}</p>
        </div>
        <div class="space-y-2">
          <Label html-for="address">Address</Label>
          <Input id="address" v-model="form.address" />
          <p v-if="form.errors.address" class="text-sm text-destructive">{{ form.errors.address }}</p>
        </div>
        <div class="space-y-2">
          <Label html-for="contact_person">Contact person</Label>
          <Input id="contact_person" v-model="form.contact_person" />
        </div>
        <div class="space-y-2">
          <Label html-for="notes">Notes</Label>
          <Input id="notes" v-model="form.notes" />
        </div>
      </FormDialog>
    </div>
  </AppLayout>
</template>
