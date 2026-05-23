<script setup lang="ts">
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { Toaster } from 'sonner'
import { Button } from '@/components/ui/button'
import type { User } from '@/types/models'

const page = usePage()
const user = computed(() => (page.props.auth as { user: User | null }).user)

const logout = () => router.post('/logout')
</script>

<template>
  <div class="min-h-screen bg-background flex flex-col">
    <header class="bg-primary text-primary-foreground px-6 py-3 flex items-center justify-between">
      <h1 class="font-semibold">EyeTech POS</h1>
      <div class="flex items-center gap-3 text-sm">
        <span>{{ user?.name }}</span>
        <Button variant="secondary" size="sm" @click="logout">Sign out</Button>
      </div>
    </header>
    <main class="flex-1 p-6">
      <slot />
    </main>
    <Toaster position="top-right" richColors />
  </div>
</template>
