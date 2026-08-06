<script setup lang="ts">
import { computed, ref } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import type { Category, Product } from '@/types/models'

const props = defineProps<{ products: Product[]; categories: Category[] }>()
const emit = defineEmits<{ select: [product: Product] }>()

const search = ref('')
const selectedCategoryId = ref<number | null>(null)
const brokenImages = ref<Set<number>>(new Set())

function onImageError(productId: number) {
  brokenImages.value.add(productId)
}

const filtered = computed(() =>
  props.products.filter((p) => {
    const matchesSearch =
      !search.value ||
      p.name.toLowerCase().includes(search.value.toLowerCase()) ||
      p.sku.toLowerCase().includes(search.value.toLowerCase())
    const matchesCategory =
      !selectedCategoryId.value || p.category_id === selectedCategoryId.value
    return matchesSearch && matchesCategory
  }),
)
</script>

<template>
  <div class="space-y-4">
    <Input
      v-model="search"
      placeholder="Search by name or SKU..."
      class="w-full"
    />
    <div class="flex gap-2 flex-wrap">
      <Button
        :variant="selectedCategoryId === null ? 'default' : 'outline'"
        size="sm"
        @click="selectedCategoryId = null"
      >
        All
      </Button>
      <Button
        v-for="c in categories"
        :key="c.id"
        :variant="selectedCategoryId === c.id ? 'default' : 'outline'"
        size="sm"
        @click="selectedCategoryId = c.id"
      >
        {{ c.name }}
      </Button>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
      <Card
        v-for="p in filtered"
        :key="p.id"
        class="cursor-pointer hover:border-primary transition-colors overflow-hidden"
        @click="emit('select', p)"
      >
        <div class="aspect-square bg-muted flex items-center justify-center overflow-hidden">
          <img
            v-if="p.image_url && !brokenImages.has(p.id)"
            :src="p.image_url"
            :alt="p.name"
            class="w-full h-full object-cover"
            loading="lazy"
            @error="onImageError(p.id)"
          />
          <div v-else class="text-muted-foreground text-xs px-2 text-center">No image</div>
        </div>
        <CardContent class="p-3">
          <div class="font-medium text-sm line-clamp-2">{{ p.name }}</div>
          <div class="text-xs text-muted-foreground">{{ p.sku }}</div>
          <div class="mt-2 flex items-center justify-between">
            <span class="font-semibold">{{ p.selling_price.toFixed(2) }}</span>
            <span
              :class="[
                'text-xs',
                p.stock_quantity <= p.min_stock_level
                  ? 'text-destructive'
                  : 'text-muted-foreground',
              ]"
            >
              {{ p.stock_quantity }} left
            </span>
          </div>
        </CardContent>
      </Card>
    </div>
    <p v-if="filtered.length === 0" class="text-center text-muted-foreground py-8">
      No products match.
    </p>
  </div>
</template>
