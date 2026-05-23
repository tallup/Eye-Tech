<script setup lang="ts">
import { cn } from '@/lib/utils'

defineProps<{ for?: string }>()
</script>

<template>
  <label
    :for="for"
    :class="cn('text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70')"
  >
    <slot />
  </label>
</template>
