<script setup lang="ts">
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import CartLine from './CartLine.vue'
import type { CartLine as Line, PaymentMethod } from '@/types/models'

const props = defineProps<{
  lines: Line[]
  subtotal: number
  total: number
}>()

const emit = defineEmits<{
  'update:quantity': [productId: number, qty: number]
  remove: [productId: number]
  clear: []
}>()

const customerName = ref('')
const customerPhone = ref('')
const paymentMethod = ref<PaymentMethod>('cash')
const processing = ref(false)

const canCheckout = computed(() => props.lines.length > 0 && !processing.value)

function checkout() {
  if (!canCheckout.value) return
  processing.value = true

  router.post(
    '/app/pos/checkout',
    {
      customer_name: customerName.value || null,
      customer_phone: customerPhone.value || null,
      payment_method: paymentMethod.value,
      items: props.lines.map((l) => ({
        product_id: l.product_id,
        quantity: l.quantity,
      })),
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        emit('clear')
        customerName.value = ''
        customerPhone.value = ''
      },
      onError: () => toast.error('Checkout failed — see errors above.'),
      onFinish: () => {
        processing.value = false
      },
    },
  )
}
</script>

<template>
  <Card class="sticky top-4">
    <CardHeader>
      <CardTitle>Cart</CardTitle>
    </CardHeader>
    <CardContent class="space-y-4">
      <div v-if="lines.length === 0" class="text-center text-muted-foreground py-8">
        Cart is empty. Tap a product to add.
      </div>
      <div v-else class="max-h-80 overflow-y-auto">
        <CartLine
          v-for="line in lines"
          :key="line.product_id"
          :line="line"
          @update:quantity="(id, qty) => emit('update:quantity', id, qty)"
          @remove="(id) => emit('remove', id)"
        />
      </div>

      <div v-if="lines.length > 0" class="space-y-2 pt-2 border-t">
        <div class="flex justify-between">
          <span>Subtotal</span>
          <span>{{ subtotal.toFixed(2) }}</span>
        </div>
        <div class="flex justify-between text-lg font-bold">
          <span>Total</span>
          <span>{{ total.toFixed(2) }}</span>
        </div>
      </div>

      <div class="space-y-2">
        <div class="space-y-1">
          <Label html-for="cust-name">Customer name (optional)</Label>
          <Input id="cust-name" v-model="customerName" />
        </div>
        <div class="space-y-1">
          <Label html-for="cust-phone">Phone (optional)</Label>
          <Input id="cust-phone" v-model="customerPhone" />
        </div>
        <div class="space-y-1">
          <Label html-for="pay-method">Payment</Label>
          <select
            id="pay-method"
            v-model="paymentMethod"
            class="w-full rounded-md border border-input bg-background h-10 px-3 text-sm"
          >
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="mobile_money">Mobile money</option>
            <option value="bank_transfer">Bank transfer</option>
          </select>
        </div>
      </div>

      <Button :disabled="!canCheckout" class="w-full" size="lg" @click="checkout">
        {{ processing ? 'Processing...' : `Checkout — ${total.toFixed(2)}` }}
      </Button>
    </CardContent>
  </Card>
</template>
