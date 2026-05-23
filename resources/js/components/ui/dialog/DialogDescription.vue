<script setup lang="ts">
import { DialogDescription, type DialogDescriptionProps, useForwardProps } from 'radix-vue'

const props = defineProps<DialogDescriptionProps>()
const forwarded = useForwardProps(props)
</script>

<template>
  <DialogDescription v-bind="forwarded" class="text-sm text-muted-foreground">
    <slot />
  </DialogDescription>
</template>
