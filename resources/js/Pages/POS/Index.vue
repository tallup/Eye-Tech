<script setup lang="ts">
import PosLayout from '@/Layouts/PosLayout.vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
</script>

<template>
  <PosLayout>
    <Card>
      <CardHeader>
        <CardTitle>POS</CardTitle>
      </CardHeader>
      <CardContent>
        <p class="text-muted-foreground">Phase 1 ships the full POS here.</p>
      </CardContent>
    </Card>
  </PosLayout>
</template>
