import { describe, expect, it } from 'vitest'
import { usePosCart } from '@/Pages/POS/composables/usePosCart'
import type { Product } from '@/types/models'

const product = (over: Partial<Product> = {}): Product => ({
  id: 1, name: 'Frame', sku: 'F-1', description: null,
  selling_price: 100, cost_price: 50, stock_quantity: 5, min_stock_level: 1,
  category_id: 1, supplier_id: 1, category: null,
  brand: null, model: null, image_url: null, is_active: true,
  ...over,
})

describe('usePosCart', () => {
  it('starts empty', () => {
    const cart = usePosCart()
    expect(cart.lines.value).toEqual([])
    expect(cart.subtotal.value).toBe(0)
  })

  it('addItem creates a line', () => {
    const cart = usePosCart()
    cart.addItem(product())
    expect(cart.lines.value).toHaveLength(1)
    expect(cart.lines.value[0].quantity).toBe(1)
  })

  it('addItem twice increments qty on same product', () => {
    const cart = usePosCart()
    cart.addItem(product())
    cart.addItem(product())
    expect(cart.lines.value).toHaveLength(1)
    expect(cart.lines.value[0].quantity).toBe(2)
  })

  it('setQuantity updates line; 0 removes it', () => {
    const cart = usePosCart()
    cart.addItem(product())
    cart.setQuantity(1, 3)
    expect(cart.lines.value[0].quantity).toBe(3)
    cart.setQuantity(1, 0)
    expect(cart.lines.value).toHaveLength(0)
  })

  it('subtotal sums lines', () => {
    const cart = usePosCart()
    cart.addItem(product({ id: 1, selling_price: 100 }))
    cart.addItem(product({ id: 2, selling_price: 50 }))
    expect(cart.subtotal.value).toBe(150)
  })

  it('addItem refuses to exceed available stock', () => {
    const cart = usePosCart()
    cart.addItem(product({ stock_quantity: 1 }))
    const ok = cart.addItem(product({ stock_quantity: 1 }))
    expect(ok).toBe(false)
    expect(cart.lines.value[0].quantity).toBe(1)
  })

  it('clear empties the cart', () => {
    const cart = usePosCart()
    cart.addItem(product())
    cart.clear()
    expect(cart.lines.value).toEqual([])
  })
})
