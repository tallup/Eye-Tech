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
          <Link href="/app/sales" class="hover:underline">Sales</Link>
          <Link v-if="isAdmin" href="/app/products" class="hover:underline">Products</Link>
          <Link v-if="isAdmin" href="/app/categories" class="hover:underline">Categories</Link>
          <Link v-if="isAdmin" href="/app/suppliers" class="hover:underline">Suppliers</Link>
          <Link v-if="isAdmin" href="/app/stock-movements" class="hover:underline">Stock</Link>
          <Link v-if="isAdmin" href="/app/services" class="hover:underline">Services</Link>
          <Link href="/app/service-requests" class="hover:underline">Requests</Link>
          <Link v-if="isAdmin" href="/app/users" class="hover:underline">Users</Link>
          <Link v-if="isAdmin" href="/app/reports" class="hover:underline">Reports</Link>
          <Link href="/app/profile" class="hover:underline">{{ user?.name }}</Link>
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
