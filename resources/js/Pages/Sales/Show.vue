<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
defineProps<{ sale: any }>()
</script>

<template>
  <AppLayout>
    <pre>{{ sale }}</pre>
  </AppLayout>
</template>
