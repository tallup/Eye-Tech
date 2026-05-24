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
import { useAuth } from '@/composables/useAuth'

interface ManagedUser {
  id: number
  name: string
  email: string
  role: 'admin' | 'cashier'
  phone: string | null
  created_at: string
}

defineProps<{
  users: { data: ManagedUser[]; links: any[]; meta: any }
}>()

const { user: currentUser } = useAuth()

const dialogOpen = ref(false)
const editing = ref<ManagedUser | null>(null)

const form = useForm({
  name: '',
  email: '',
  role: 'cashier' as 'admin' | 'cashier',
  phone: '',
  password: '',
  password_confirmation: '',
})

function openCreate() {
  editing.value = null
  form.reset()
  dialogOpen.value = true
}

function openEdit(u: ManagedUser) {
  editing.value = u
  form.name = u.name
  form.email = u.email
  form.role = u.role
  form.phone = u.phone ?? ''
  form.password = ''
  form.password_confirmation = ''
  dialogOpen.value = true
}

function submit() {
  const onSuccess = () => {
    dialogOpen.value = false
    toast.success(editing.value ? 'User updated' : 'User created')
  }
  if (editing.value) {
    form.put(`/app/users/${editing.value.id}`, { onSuccess, preserveScroll: true })
  } else {
    form.post('/app/users', { onSuccess, preserveScroll: true })
  }
}

function destroy(u: ManagedUser) {
  if (!confirm(`Delete ${u.name}?`)) return
  router.delete(`/app/users/${u.id}`, {
    preserveScroll: true,
    onSuccess: () => toast.success('User deleted'),
  })
}

const columns = [
  { key: 'name', label: 'Name' },
  { key: 'email', label: 'Email' },
  { key: 'role', label: 'Role' },
  { key: 'phone', label: 'Phone', render: (u: ManagedUser) => u.phone ?? '—' },
  { key: 'actions', label: '' },
]
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Users</h1>
        <Button @click="openCreate">+ New user</Button>
      </div>

      <DataTable :data="users" :columns="columns">
        <template #cell-role="{ row }">
          <span :class="['inline-block px-2 py-1 rounded text-xs font-medium', row.role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800']">
            {{ row.role }}
          </span>
        </template>
        <template #cell-actions="{ row }">
          <div class="flex gap-2 justify-end">
            <Button variant="outline" size="sm" @click="openEdit(row)">Edit</Button>
            <Button
              v-if="currentUser && row.id !== currentUser.id"
              variant="destructive"
              size="sm"
              @click="destroy(row)"
            >Delete</Button>
          </div>
        </template>
      </DataTable>

      <FormDialog
        :open="dialogOpen"
        :title="editing ? 'Edit user' : 'New user'"
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
          <Label html-for="role">Role</Label>
          <select id="role" v-model="form.role" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
            <option value="cashier">Cashier</option>
            <option value="admin">Admin</option>
          </select>
          <p v-if="form.errors.role" class="text-sm text-destructive">{{ form.errors.role }}</p>
        </div>
        <div class="space-y-2">
          <Label html-for="phone">Phone</Label>
          <Input id="phone" v-model="form.phone" />
        </div>
        <div class="space-y-2">
          <Label html-for="password">{{ editing ? 'New password (leave blank to keep)' : 'Password' }}</Label>
          <Input id="password" type="password" v-model="form.password" />
          <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
        </div>
        <div class="space-y-2">
          <Label html-for="password_confirmation">Confirm password</Label>
          <Input id="password_confirmation" type="password" v-model="form.password_confirmation" />
        </div>
      </FormDialog>
    </div>
  </AppLayout>
</template>
