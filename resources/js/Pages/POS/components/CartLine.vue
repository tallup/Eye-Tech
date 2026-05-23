<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import type { CartLine as Line } from '@/types/models'

const props = defineProps<{ line: Line }>()
const emit = defineEmits<{
  'update:quantity': [productId: number, qty: number]
  remove: [productId: number]
}>()

function updateQty(value: string | number) {
  const n = typeof value === 'number' ? value : Number.parseInt(value, 10)
  if (!Number.isNaN(n)) emit('update:quantity', props.line.product_id, n)
}
</script>

<template>
  <div class="flex items-center gap-2 py-2 border-b last:border-b-0">
    <div class="flex-1 min-w-0">
      <div class="font-medium truncate">{{ line.name }}</div>
      <div class="text-xs text-muted-foreground">{{ line.unit_price.toFixed(2) }} ea</div>
    </div>
    <Button
      variant="outline"
      size="sm"
      @click="emit('update:quantity', line.product_id, line.quantity - 1)"
    >
      −
    </Button>
    <Input
      type="number"
      :model-value="line.quantity"
      class="w-16 text-center"
      @update:model-value="updateQty"
    />
    <Button
      variant="outline"
      size="sm"
      @click="emit('update:quantity', line.product_id, line.quantity + 1)"
    >
      +
    </Button>
    <div class="w-24 text-right font-semibold">
      {{ (line.unit_price * line.quantity).toFixed(2) }}
    </div>
    <Button variant="ghost" size="sm" @click="emit('remove', line.product_id)">
      ×
    </Button>
  </div>
</template>
