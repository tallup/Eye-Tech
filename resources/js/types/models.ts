export type Role = 'admin' | 'cashier'

export interface User {
  id: number
  name: string
  email: string
  role: Role
}

export interface Category {
  id: number
  name: string
  slug: string
  icon: string | null
}

export interface Product {
  id: number
  name: string
  sku: string
  description: string | null
  selling_price: number
  cost_price: number
  stock_quantity: number
  min_stock_level: number
  category_id: number
  supplier_id: number
  category: Category | null
  brand: string | null
  model: string | null
  image_url: string | null
  is_active: boolean
}

export type PaymentMethod = 'cash' | 'card' | 'mobile_money' | 'bank_transfer'
export type SaleStatus = 'pending' | 'completed' | 'cancelled' | 'refunded'

export interface SaleItem {
  id: number
  sale_id: number
  product_id: number | null
  item_name: string
  item_sku: string | null
  item_description: string | null
  quantity: number
  unit_price: number
  discount_amount: number
  total_price: number
}

export interface Sale {
  id: number
  sale_number: string
  customer_name: string | null
  customer_phone: string | null
  customer_email: string | null
  payment_method: PaymentMethod
  status: SaleStatus
  subtotal: number
  tax_amount: number
  discount_amount: number
  total_amount: number
  notes: string | null
  user_id: number | null
  user: { id: number; name: string } | null
  items: SaleItem[]
  created_at: string
}

export interface Service {
  id: number
  name: string
  slug: string
  description: string
  price: number
  estimated_duration: number | null
  category: string | null
  is_active: boolean
  is_featured: boolean
  created_at: string
}

export interface CartLine {
  product_id: number
  name: string
  sku: string | null
  unit_price: number
  quantity: number
  available_stock: number
}
