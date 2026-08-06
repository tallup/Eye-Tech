<script setup lang="ts">
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import type { Product } from '@/types/models'

const props = defineProps<{
  product: Product | null
  categories: { id: number; name: string }[]
  suppliers: { id: number; name: string }[]
}>()

const isEditing = !!props.product

const form = useForm({
  name: props.product?.name ?? '',
  sku: props.product?.sku ?? '',
  description: props.product?.description ?? '',
  category_id: props.product?.category_id ?? (props.categories[0]?.id ?? null),
  supplier_id: props.product?.supplier_id ?? (props.suppliers[0]?.id ?? null),
  cost_price: props.product?.cost_price ?? 0,
  selling_price: props.product?.selling_price ?? 0,
  stock_quantity: props.product?.stock_quantity ?? 0,
  min_stock_level: props.product?.min_stock_level ?? 0,
  brand: props.product?.brand ?? '',
  model: props.product?.model ?? '',
  is_active: props.product?.is_active ?? true,
  image: null as File | null,
  _method: isEditing ? 'put' : 'post',
})

const imagePreview = ref<string | null>(props.product?.image_url ?? null)

function onImageChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) {
    form.image = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

function submit() {
  const url = isEditing ? `/app/products/${props.product!.id}` : '/app/products'
  form.post(url, {
    forceFormData: true,
    onSuccess: () => {
      toast.success(isEditing ? 'Product updated' : 'Product created')
      router.visit('/app/products')
    },
  })
}
</script>

<template>
  <AppLayout>
    <div class="max-w-3xl mx-auto space-y-6">
      <h1 class="text-3xl font-bold">{{ isEditing ? 'Edit product' : 'New product' }}</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <Card>
          <CardHeader><CardTitle>Basic info</CardTitle></CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label html-for="name">Name</Label>
                <Input id="name" v-model="form.name" />
                <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
              </div>
              <div class="space-y-2">
                <Label html-for="sku">SKU</Label>
                <Input id="sku" v-model="form.sku" />
                <p v-if="form.errors.sku" class="text-sm text-destructive">{{ form.errors.sku }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <Label html-for="description">Description</Label>
              <textarea id="description" v-model="form.description" rows="3" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label html-for="category_id">Category</Label>
                <select id="category_id" v-model="form.category_id" class="w-full rounded-md border border-input bg-background h-10 px-3 text-sm">
                  <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>
              <div class="space-y-2">
                <Label html-for="supplier_id">Supplier</Label>
                <select id="supplier_id" v-model="form.supplier_id" class="w-full rounded-md border border-input bg-background h-10 px-3 text-sm">
                  <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader><CardTitle>Pricing & Stock</CardTitle></CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label html-for="cost_price">Cost price</Label>
                <Input id="cost_price" type="number" step="0.01" v-model="form.cost_price" />
              </div>
              <div class="space-y-2">
                <Label html-for="selling_price">Selling price</Label>
                <Input id="selling_price" type="number" step="0.01" v-model="form.selling_price" />
              </div>
              <div class="space-y-2">
                <Label html-for="stock_quantity">Stock</Label>
                <Input id="stock_quantity" type="number" v-model="form.stock_quantity" />
              </div>
              <div class="space-y-2">
                <Label html-for="min_stock_level">Min stock level</Label>
                <Input id="min_stock_level" type="number" v-model="form.min_stock_level" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader><CardTitle>Media & Meta</CardTitle></CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label html-for="brand">Brand</Label>
                <Input id="brand" v-model="form.brand" />
              </div>
              <div class="space-y-2">
                <Label html-for="model">Model</Label>
                <Input id="model" v-model="form.model" />
              </div>
            </div>
            <div class="space-y-2">
              <Label html-for="image">Image</Label>
              <input id="image" type="file" accept="image/*" @change="onImageChange" class="block w-full text-sm" />
              <p v-if="form.errors.image" class="text-sm text-destructive">{{ form.errors.image }}</p>
              <div v-if="imagePreview" class="mt-2">
                <img :src="imagePreview" class="w-32 h-32 object-cover rounded border" />
              </div>
            </div>
            <div class="flex items-center gap-2">
              <input id="is_active" type="checkbox" v-model="form.is_active" />
              <Label html-for="is_active">Active (visible at POS)</Label>
            </div>
          </CardContent>
        </Card>

        <div class="flex justify-end gap-2">
          <Button type="button" variant="outline" @click="router.visit('/app/products')">Cancel</Button>
          <Button type="submit" :disabled="form.processing">{{ form.processing ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}</Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
