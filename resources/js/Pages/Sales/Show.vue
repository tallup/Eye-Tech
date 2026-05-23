<script setup lang="ts">
import { onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import type { Sale } from '@/types/models'

defineProps<{ sale: { data: Sale } }>()

const page = usePage()

onMounted(() => {
  if ((page.props.flash as any)?.print) {
    setTimeout(() => window.print(), 100)
  }
})

function triggerPrint() {
  window.print()
}
</script>

<template>
  <AppLayout>
    <div class="max-w-2xl mx-auto print:max-w-full">
      <div class="flex justify-between items-center mb-6 print:hidden">
        <h1 class="text-3xl font-bold">Receipt</h1>
        <Button variant="outline" @click="triggerPrint">Print</Button>
      </div>

      <Card class="receipt">
        <CardHeader class="text-center">
          <CardTitle>EyeTech</CardTitle>
          <p class="text-sm text-muted-foreground">Sale {{ sale.data.sale_number }}</p>
          <p class="text-xs text-muted-foreground">{{ new Date(sale.data.created_at).toLocaleString() }}</p>
        </CardHeader>
        <CardContent class="space-y-4">
          <div v-if="sale.data.customer_name" class="text-sm">
            <strong>Customer:</strong> {{ sale.data.customer_name }}
            <span v-if="sale.data.customer_phone">— {{ sale.data.customer_phone }}</span>
          </div>

          <div class="border-t border-b py-2 space-y-1">
            <div v-for="item in sale.data.items" :key="item.id" class="flex justify-between text-sm">
              <div class="flex-1">
                {{ item.item_name }}
                <span class="text-muted-foreground"> x {{ item.quantity }}</span>
              </div>
              <div class="font-medium">{{ Number(item.total_price).toFixed(2) }}</div>
            </div>
          </div>

          <div class="space-y-1 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span>{{ Number(sale.data.subtotal).toFixed(2) }}</span></div>
            <div v-if="Number(sale.data.tax_amount) > 0" class="flex justify-between"><span>Tax</span><span>{{ Number(sale.data.tax_amount).toFixed(2) }}</span></div>
            <div v-if="Number(sale.data.discount_amount) > 0" class="flex justify-between"><span>Discount</span><span>-{{ Number(sale.data.discount_amount).toFixed(2) }}</span></div>
            <div class="flex justify-between font-bold text-lg pt-1 border-t"><span>Total</span><span>{{ Number(sale.data.total_amount).toFixed(2) }}</span></div>
            <div class="text-xs text-muted-foreground pt-2">Payment: {{ sale.data.payment_method.replace('_', ' ') }}</div>
            <div v-if="sale.data.user" class="text-xs text-muted-foreground">Cashier: {{ sale.data.user.name }}</div>
          </div>

          <p class="text-center text-xs text-muted-foreground pt-4">Thank you!</p>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<style>
@media print {
  body { background: white !important; }
  .print\:hidden { display: none !important; }
  nav, header { display: none !important; }
  .receipt { box-shadow: none !important; border: none !important; }
}
</style>
