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

interface Category {
  id: number
  name: string
  slug: string
  description: string | null
  icon: string | null
}

defineProps<{
  categories: { data: Category[]; links: any[]; meta: any }
}>()

const dialogOpen = ref(false)
const editing = ref<Category | null>(null)

const form = useForm({
  name: '',
  slug: '',
  description: '',
  icon: '',
})

function openCreate() {
  editing.value = null
  form.reset()
  dialogOpen.value = true
}

function openEdit(category: Category) {
  editing.value = category
  form.name = category.name
  form.slug = category.slug
  form.description = category.description ?? ''
  form.icon = category.icon ?? ''
  dialogOpen.value = true
}

function autoSlug() {
  if (!form.slug && form.name) {
    form.slug = form.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')
  }
}

function submit() {
  const onSuccess = () => {
    dialogOpen.value = false
    toast.success(editing.value ? 'Category updated' : 'Category created')
  }
  if (editing.value) {
    form.put(`/app/categories/${editing.value.id}`, { onSuccess, preserveScroll: true })
  } else {
    form.post('/app/categories', { onSuccess, preserveScroll: true })
  }
}

function destroy(category: Category) {
  if (!confirm(`Delete ${category.name}?`)) return
  router.delete(`/app/categories/${category.id}`, {
    preserveScroll: true,
    onSuccess: () => toast.success('Category deleted'),
  })
}

const columns = [
  { key: 'name', label: 'Name' },
  { key: 'slug', label: 'Slug' },
  { key: 'description', label: 'Description', render: (c: Category) => (c.description ? (c.description.length > 60 ? c.description.slice(0, 60) + '…' : c.description) : '—') },
  { key: 'actions', label: '' },
]
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Categories</h1>
        <Button @click="openCreate">+ New category</Button>
      </div>

      <DataTable :data="categories" :columns="columns">
        <template #cell-actions="{ row }">
          <div class="flex gap-2 justify-end">
            <Button variant="outline" size="sm" @click="openEdit(row)">Edit</Button>
            <Button variant="destructive" size="sm" @click="destroy(row)">Delete</Button>
          </div>
        </template>
      </DataTable>

      <FormDialog
        :open="dialogOpen"
        :title="editing ? 'Edit category' : 'New category'"
        :processing="form.processing"
        @update:open="dialogOpen = $event"
        @submit="submit"
      >
        <div class="space-y-2">
          <Label html-for="name">Name</Label>
          <Input id="name" v-model="form.name" @blur="autoSlug" />
          <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
        </div>
        <div class="space-y-2">
          <Label html-for="slug">Slug</Label>
          <Input id="slug" v-model="form.slug" />
          <p v-if="form.errors.slug" class="text-sm text-destructive">{{ form.errors.slug }}</p>
        </div>
        <div class="space-y-2">
          <Label html-for="description">Description</Label>
          <Input id="description" v-model="form.description" />
        </div>
        <div class="space-y-2">
          <Label html-for="icon">Icon</Label>
          <Input id="icon" v-model="form.icon" />
        </div>
      </FormDialog>
    </div>
  </AppLayout>
</template>
