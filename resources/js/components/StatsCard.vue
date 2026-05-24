<script setup lang="ts">
defineProps<{ title: string; value: string | number; description?: string }>()
</script>

<template>
  <div class="rounded-lg border bg-card p-6">
    <p class="text-sm font-medium text-muted-foreground">{{ title }}</p>
    <p class="text-3xl font-bold mt-1">{{ value }}</p>
    <p v-if="description" class="text-xs text-muted-foreground mt-1">{{ description }}</p>
  </div>
</template>
