<script setup lang="ts">
import PosLayout from '@/Layouts/PosLayout.vue'
import ProductGrid from './components/ProductGrid.vue'
import Cart from './components/Cart.vue'
import { usePosCart } from './composables/usePosCart'
import type { Category, Product } from '@/types/models'

const props = defineProps<{
  products: { data: Product[] } | Product[]
  categories: { data: Category[] } | Category[]
}>()

const productList = Array.isArray(props.products) ? props.products : props.products.data
const categoryList = Array.isArray(props.categories) ? props.categories : props.categories.data

const { lines, subtotal, total, addItem, setQuantity, remove, clear } = usePosCart()
</script>

<template>
  <PosLayout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2">
        <ProductGrid
          :products="productList"
          :categories="categoryList"
          @select="addItem"
        />
      </div>
      <div>
        <Cart
          :lines="lines"
          :subtotal="subtotal"
          :total="total"
          @update:quantity="setQuantity"
          @remove="remove"
          @clear="clear"
        />
      </div>
    </div>
  </PosLayout>
</template>
