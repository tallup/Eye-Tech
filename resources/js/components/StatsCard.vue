<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  title: string
  value: string | number
  description?: string
  delta?: number | null
  accent?: 'primary' | 'success' | 'warning' | 'danger' | 'info' | 'neutral'
  icon?: string
}>()

const accentClass = computed(() => {
  switch (props.accent) {
    case 'success': return 'border-l-4 border-l-green-500'
    case 'warning': return 'border-l-4 border-l-amber-500'
    case 'danger': return 'border-l-4 border-l-destructive'
    case 'info': return 'border-l-4 border-l-blue-500'
    case 'primary': return 'border-l-4 border-l-primary'
    default: return ''
  }
})

const deltaClass = computed(() => {
  if (props.delta === null || props.delta === undefined) return ''
  return props.delta >= 0 ? 'text-green-600' : 'text-destructive'
})

const deltaArrow = computed(() => {
  if (props.delta === null || props.delta === undefined) return ''
  return props.delta >= 0 ? '▲' : '▼'
})
</script>

<template>
  <div :class="['rounded-lg border bg-card p-5 transition-shadow hover:shadow-md', accentClass]">
    <div class="flex items-start justify-between">
      <p class="text-xs font-medium text-muted-foreground uppercase tracking-wide">{{ title }}</p>
      <span v-if="icon" class="text-muted-foreground text-lg">{{ icon }}</span>
    </div>
    <p class="text-2xl font-bold mt-2 truncate">{{ value }}</p>
    <div class="flex items-center gap-2 mt-1 min-h-[1.25rem]">
      <span v-if="delta !== null && delta !== undefined" :class="['text-xs font-medium', deltaClass]">
        {{ deltaArrow }} {{ Math.abs(delta).toFixed(1) }}%
      </span>
      <span v-if="description" class="text-xs text-muted-foreground">{{ description }}</span>
    </div>
  </div>
</template>
