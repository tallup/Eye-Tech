<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h1 class="text-3xl font-bold">Dashboard</h1>
      <Card>
        <CardHeader>
          <CardTitle>Welcome to the new EyeTech admin</CardTitle>
        </CardHeader>
        <CardContent>
          <p class="text-muted-foreground">Stats and charts coming in Phase 2.</p>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
