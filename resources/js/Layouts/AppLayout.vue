<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { Toaster } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import type { User } from '@/types/models'

const page = usePage()
const user = computed(() => (page.props.auth as { user: User | null }).user)
const isAdmin = computed(() => user.value?.role === 'admin')

const logout = () => router.post('/logout')
</script>

<template>
  <div class="min-h-screen bg-muted/40">
    <header class="bg-background border-b">
      <div class="container mx-auto flex items-center justify-between px-4 py-3">
        <Link href="/app/dashboard" class="font-semibold">EyeTech</Link>
        <nav class="flex items-center gap-4 text-sm">
          <Link v-if="isAdmin" href="/app/dashboard" class="hover:underline">Dashboard</Link>
          <Link href="/app/pos" class="hover:underline">POS</Link>
          <span class="text-muted-foreground">{{ user?.name }}</span>
          <Button variant="outline" size="sm" @click="logout">Sign out</Button>
        </nav>
      </div>
    </header>
    <main class="container mx-auto px-4 py-8">
      <slot />
    </main>
    <Toaster position="top-right" richColors />
  </div>
</template>
