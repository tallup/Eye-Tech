<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center px-4">
    <h1 class="text-6xl font-bold">404</h1>
    <p class="text-lg text-muted-foreground mt-2">Page not found.</p>
    <Link href="/" class="mt-6">
      <Button>Go home</Button>
    </Link>
  </div>
</template>
