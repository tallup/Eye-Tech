<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatsCard from '@/components/StatsCard.vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'

interface ChartPoint { date: string; total: number; count: number }
interface TopProduct { product_id: number | null; name: string; sku: string | null; units_sold: number; revenue: number }
interface TopCategory { id: number; name: string; revenue: number; units: number }
interface PaymentRow { method: string; count: number; total: number }
interface RecentSale { id: number; sale_number: string; customer_name: string | null; total_amount: number; payment_method: string; status: string; cashier: string | null; created_at: string }
interface RecentMovement { id: number; product_name: string | null; product_sku: string | null; movement_type: string; quantity: number; previous_quantity: number; new_quantity: number; user_name: string | null; created_at: string }
interface LowStockProduct { id: number; name: string; sku: string; stock_quantity: number; min_stock_level: number; category: string | null }

interface Stats {
  today_sales_total: number
  today_sales_count: number
  yesterday_sales_total: number
  delta_today_pct: number | null
  week_sales_total: number
  week_sales_count: number
  delta_week_pct: number | null
  month_sales_total: number
  month_sales_count: number
  total_products: number
  total_stock_units: number
  inventory_value_cost: number
  inventory_value_retail: number
  low_stock_count: number
  out_of_stock_count: number
}

const props = defineProps<{
  stats: Stats
  sales_chart: ChartPoint[]
  top_products: TopProduct[]
  top_categories: TopCategory[]
  payment_breakdown: PaymentRow[]
  recent_sales: RecentSale[]
  recent_movements: RecentMovement[]
  low_stock_products: LowStockProduct[]
}>()

function money(v: number): string {
  return new Intl.NumberFormat('en-GB', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(v)
}

function shortMoney(v: number): string {
  if (v >= 1000000) return (v / 1000000).toFixed(1) + 'M'
  if (v >= 1000) return (v / 1000).toFixed(1) + 'k'
  return v.toFixed(0)
}

// 14-day sales line chart
const chartMaxTotal = computed(() => Math.max(1, ...props.sales_chart.map(p => p.total)))
const chartWidth = 720
const chartHeight = 200
const padding = { top: 20, right: 20, bottom: 30, left: 50 }
const plotW = chartWidth - padding.left - padding.right
const plotH = chartHeight - padding.top - padding.bottom

const points = computed(() => {
  const n = props.sales_chart.length
  return props.sales_chart.map((p, i) => ({
    x: padding.left + (i / Math.max(1, n - 1)) * plotW,
    y: padding.top + plotH - (p.total / chartMaxTotal.value) * plotH,
    label: p.date,
    total: p.total,
    count: p.count,
  }))
})

const linePath = computed(() => points.value.map((pt, i) => `${i === 0 ? 'M' : 'L'} ${pt.x} ${pt.y}`).join(' '))
const areaPath = computed(() => {
  const pts = points.value
  if (pts.length === 0) return ''
  const start = `M ${pts[0].x} ${padding.top + plotH}`
  const lines = pts.map(pt => `L ${pt.x} ${pt.y}`).join(' ')
  const end = `L ${pts[pts.length - 1].x} ${padding.top + plotH} Z`
  return `${start} ${lines} ${end}`
})

// Top products bar chart
const topProductMax = computed(() => Math.max(1, ...props.top_products.map(p => p.units_sold)))

// Payment breakdown total
const paymentTotal = computed(() => props.payment_breakdown.reduce((s, p) => s + p.total, 0))

function paymentColor(method: string): string {
  const colors: Record<string, string> = {
    cash: 'bg-green-500',
    card: 'bg-blue-500',
    mobile_money: 'bg-purple-500',
    bank_transfer: 'bg-amber-500',
  }
  return colors[method] ?? 'bg-muted-foreground'
}

function movementBadge(t: string): string {
  const map: Record<string, string> = {
    in: 'bg-green-100 text-green-800',
    out: 'bg-orange-100 text-orange-800',
    sale: 'bg-blue-100 text-blue-800',
    adjustment: 'bg-yellow-100 text-yellow-800',
    transfer: 'bg-purple-100 text-purple-800',
    purchase: 'bg-cyan-100 text-cyan-800',
  }
  return map[t] ?? 'bg-muted text-foreground'
}

function timeAgo(iso: string): string {
  const d = new Date(iso)
  const diffMs = Date.now() - d.getTime()
  const diffMin = Math.floor(diffMs / 60000)
  if (diffMin < 1) return 'just now'
  if (diffMin < 60) return `${diffMin}m ago`
  const diffHr = Math.floor(diffMin / 60)
  if (diffHr < 24) return `${diffHr}h ago`
  const diffDay = Math.floor(diffHr / 24)
  if (diffDay < 7) return `${diffDay}d ago`
  return d.toLocaleDateString()
}
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold">Dashboard</h1>
          <p class="text-sm text-muted-foreground mt-1">
            Everything at a glance — {{ new Date().toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long' }) }}
          </p>
        </div>
      </div>

      <!-- Sales row -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <StatsCard
          title="Today"
          :value="money(stats.today_sales_total)"
          :description="`${stats.today_sales_count} sales`"
          :delta="stats.delta_today_pct"
          accent="primary"
          icon="$"
        />
        <StatsCard
          title="This week"
          :value="money(stats.week_sales_total)"
          :description="`${stats.week_sales_count} sales`"
          :delta="stats.delta_week_pct"
          accent="success"
          icon="$"
        />
        <StatsCard
          title="This month"
          :value="money(stats.month_sales_total)"
          :description="`${stats.month_sales_count} sales`"
          accent="info"
          icon="$"
        />
        <StatsCard
          title="Avg sale today"
          :value="money(stats.today_sales_count > 0 ? stats.today_sales_total / stats.today_sales_count : 0)"
          description="Per transaction"
          accent="neutral"
        />
      </div>

      <!-- Inventory row -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <StatsCard
          title="Products"
          :value="stats.total_products"
          description="Active SKUs"
          icon="📦"
        />
        <StatsCard
          title="Stock units"
          :value="stats.total_stock_units.toLocaleString()"
          description="Total on hand"
          icon="∑"
        />
        <StatsCard
          title="Inventory value"
          :value="money(stats.inventory_value_retail)"
          :description="`Cost: ${money(stats.inventory_value_cost)}`"
          accent="info"
          icon="💰"
        />
        <StatsCard
          title="Stock alerts"
          :value="stats.low_stock_count + stats.out_of_stock_count"
          :description="`${stats.out_of_stock_count} out · ${stats.low_stock_count} low`"
          :accent="(stats.low_stock_count + stats.out_of_stock_count) > 0 ? 'danger' : 'success'"
          icon="⚠"
        />
      </div>

      <!-- Sales chart + top products row -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <Card class="lg:col-span-2">
          <CardHeader>
            <CardTitle>Sales — last 14 days</CardTitle>
          </CardHeader>
          <CardContent>
            <svg :width="chartWidth" :height="chartHeight" class="block w-full h-auto">
              <!-- Y axis grid -->
              <line v-for="i in [0, 0.25, 0.5, 0.75, 1]" :key="i"
                :x1="padding.left" :y1="padding.top + plotH * (1 - i)"
                :x2="chartWidth - padding.right" :y2="padding.top + plotH * (1 - i)"
                stroke="currentColor" stroke-opacity="0.1" />
              <!-- Area fill -->
              <path :d="areaPath" class="fill-primary/10" />
              <!-- Line -->
              <path :d="linePath" fill="none" class="stroke-primary" stroke-width="2.5" stroke-linejoin="round" />
              <!-- Points -->
              <g v-for="pt in points" :key="pt.label">
                <circle :cx="pt.x" :cy="pt.y" r="4" class="fill-primary" />
                <title>{{ pt.label }}: {{ money(pt.total) }} ({{ pt.count }} sales)</title>
              </g>
              <!-- X labels (every other) -->
              <text v-for="(pt, i) in points" :key="`x${pt.label}`"
                v-show="i % 2 === 0 || i === points.length - 1"
                :x="pt.x" :y="chartHeight - 8"
                text-anchor="middle"
                class="text-[10px] fill-muted-foreground">{{ pt.label }}</text>
              <!-- Y labels -->
              <text v-for="i in [0, 0.5, 1]" :key="`y${i}`"
                :x="padding.left - 8" :y="padding.top + plotH * (1 - i) + 4"
                text-anchor="end" class="text-[10px] fill-muted-foreground">
                {{ shortMoney(chartMaxTotal * i) }}
              </text>
            </svg>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Payment mix — this month</CardTitle>
          </CardHeader>
          <CardContent>
            <p v-if="payment_breakdown.length === 0" class="text-sm text-muted-foreground">No sales yet.</p>
            <div v-else class="space-y-3">
              <div v-for="p in payment_breakdown" :key="p.method">
                <div class="flex justify-between text-sm mb-1">
                  <span class="font-medium capitalize">{{ p.method.replace('_', ' ') }}</span>
                  <span class="text-muted-foreground">{{ money(p.total) }}</span>
                </div>
                <div class="w-full h-2 bg-muted rounded overflow-hidden">
                  <div :class="['h-full', paymentColor(p.method)]"
                    :style="`width: ${paymentTotal > 0 ? (p.total / paymentTotal * 100) : 0}%`"></div>
                </div>
                <div class="text-xs text-muted-foreground mt-1">{{ p.count }} transactions</div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Top products + Top categories -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Top products — this month</CardTitle>
          </CardHeader>
          <CardContent>
            <p v-if="top_products.length === 0" class="text-sm text-muted-foreground">No sales this month yet.</p>
            <div v-else class="space-y-2">
              <div v-for="(p, i) in top_products" :key="i" class="space-y-1">
                <div class="flex justify-between text-sm">
                  <span class="font-medium truncate">{{ p.name }}</span>
                  <span class="text-muted-foreground tabular-nums whitespace-nowrap ml-2">
                    {{ p.units_sold }} units · {{ money(p.revenue) }}
                  </span>
                </div>
                <div class="w-full h-2 bg-muted rounded overflow-hidden">
                  <div class="h-full bg-primary"
                    :style="`width: ${(p.units_sold / topProductMax) * 100}%`"></div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Top categories — this month</CardTitle>
          </CardHeader>
          <CardContent>
            <p v-if="top_categories.length === 0" class="text-sm text-muted-foreground">No data yet.</p>
            <ul v-else class="divide-y">
              <li v-for="c in top_categories" :key="c.id" class="py-2 flex items-center justify-between">
                <div>
                  <div class="font-medium">{{ c.name }}</div>
                  <div class="text-xs text-muted-foreground">{{ c.units }} units sold</div>
                </div>
                <div class="font-semibold">{{ money(c.revenue) }}</div>
              </li>
            </ul>
          </CardContent>
        </Card>
      </div>

      <!-- Recent activity + low stock -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Recent sales</CardTitle>
          </CardHeader>
          <CardContent>
            <p v-if="recent_sales.length === 0" class="text-sm text-muted-foreground">No sales yet.</p>
            <ul v-else class="divide-y">
              <li v-for="s in recent_sales" :key="s.id" class="py-2">
                <Link :href="`/app/sales/${s.id}`" class="block hover:bg-muted/30 -mx-2 px-2 rounded">
                  <div class="flex justify-between items-start">
                    <div class="min-w-0 flex-1">
                      <div class="font-medium text-sm truncate">{{ s.customer_name ?? 'Walk-in' }}</div>
                      <div class="text-xs text-muted-foreground">{{ s.sale_number }} · {{ s.cashier ?? '—' }}</div>
                    </div>
                    <div class="text-right ml-2 whitespace-nowrap">
                      <div class="font-semibold text-sm">{{ money(s.total_amount) }}</div>
                      <div class="text-xs text-muted-foreground">{{ timeAgo(s.created_at) }}</div>
                    </div>
                  </div>
                </Link>
              </li>
            </ul>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Recent stock changes</CardTitle>
          </CardHeader>
          <CardContent>
            <p v-if="recent_movements.length === 0" class="text-sm text-muted-foreground">No movements yet.</p>
            <ul v-else class="divide-y">
              <li v-for="m in recent_movements" :key="m.id" class="py-2">
                <div class="flex justify-between items-start gap-2">
                  <div class="min-w-0 flex-1">
                    <div class="font-medium text-sm truncate">{{ m.product_name ?? '—' }}</div>
                    <div class="text-xs text-muted-foreground">
                      {{ m.previous_quantity }} → {{ m.new_quantity }} · {{ m.user_name ?? '—' }}
                    </div>
                  </div>
                  <div class="text-right ml-2 whitespace-nowrap">
                    <span :class="['text-xs px-2 py-0.5 rounded font-medium', movementBadge(m.movement_type)]">
                      {{ m.movement_type }}
                    </span>
                    <div class="text-xs text-muted-foreground mt-1">{{ timeAgo(m.created_at) }}</div>
                  </div>
                </div>
              </li>
            </ul>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle class="flex justify-between items-center">
              <span>Low stock</span>
              <span v-if="low_stock_products.length > 0" class="text-xs font-normal text-destructive">
                {{ stats.out_of_stock_count + stats.low_stock_count }} items
              </span>
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p v-if="low_stock_products.length === 0" class="text-sm text-muted-foreground">All stocked.</p>
            <ul v-else class="divide-y">
              <li v-for="p in low_stock_products" :key="p.id" class="py-2">
                <Link :href="`/app/products/${p.id}/edit`" class="block hover:bg-muted/30 -mx-2 px-2 rounded">
                  <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0 flex-1">
                      <div class="font-medium text-sm truncate">{{ p.name }}</div>
                      <div class="text-xs text-muted-foreground">{{ p.sku }}<span v-if="p.category"> · {{ p.category }}</span></div>
                    </div>
                    <div class="text-right">
                      <div :class="['font-bold text-sm', p.stock_quantity === 0 ? 'text-destructive' : 'text-amber-600']">
                        {{ p.stock_quantity }}
                      </div>
                      <div class="text-xs text-muted-foreground">min {{ p.min_stock_level }}</div>
                    </div>
                  </div>
                </Link>
              </li>
            </ul>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
