<script setup lang="ts">
import { DialogTitle, type DialogTitleProps, useForwardProps } from 'radix-vue'

const props = defineProps<DialogTitleProps>()
const forwarded = useForwardProps(props)
</script>

<template>
  <DialogTitle v-bind="forwarded" class="text-lg font-semibold leading-none tracking-tight">
    <slot />
  </DialogTitle>
</template>
