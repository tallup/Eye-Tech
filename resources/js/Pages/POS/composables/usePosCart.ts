import { computed, ref } from 'vue'
import type { CartLine, Product } from '@/types/models'

export function usePosCart() {
  const lines = ref<CartLine[]>([])

  function addItem(product: Product): boolean {
    const existing = lines.value.find((l) => l.product_id === product.id)
    const currentQty = existing?.quantity ?? 0
    if (currentQty + 1 > product.stock_quantity) return false

    if (existing) {
      existing.quantity += 1
    } else {
      lines.value.push({
        product_id: product.id,
        name: product.name,
        sku: product.sku,
        unit_price: product.selling_price,
        quantity: 1,
        available_stock: product.stock_quantity,
      })
    }
    return true
  }

  function setQuantity(productId: number, qty: number) {
    const idx = lines.value.findIndex((l) => l.product_id === productId)
    if (idx === -1) return
    if (qty <= 0) {
      lines.value.splice(idx, 1)
      return
    }
    const line = lines.value[idx]
    line.quantity = Math.min(qty, line.available_stock)
  }

  function remove(productId: number) {
    lines.value = lines.value.filter((l) => l.product_id !== productId)
  }

  function clear() {
    lines.value = []
  }

  const subtotal = computed(() =>
    lines.value.reduce((sum, l) => sum + l.unit_price * l.quantity, 0),
  )
  const total = subtotal

  return { lines, addItem, setQuantity, remove, clear, subtotal, total }
}
