<script setup lang="ts">
import { cn } from '@/lib/utils'

const props = defineProps<{
  modelValue?: string | number
  type?: string
  placeholder?: string
  disabled?: boolean
  id?: string
  name?: string
  autocomplete?: string
}>()

defineEmits<{ 'update:modelValue': [value: string] }>()
</script>

<template>
  <input
    :id="id"
    :name="name"
    :type="type ?? 'text'"
    :placeholder="placeholder"
    :disabled="disabled"
    :autocomplete="autocomplete"
    :value="modelValue"
    @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
    :class="cn('flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50')"
  />
</template>
