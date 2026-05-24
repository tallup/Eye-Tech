<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatsCard from '@/components/StatsCard.vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'

interface ChartPoint { date: string; total: number }
interface LowStockProduct {
  id: number; name: string; sku: string; stock_quantity: number; min_stock_level: number; category: string | null
}

const props = defineProps<{
  stats: {
    today_sales_total: number
    today_sales_count: number
    low_stock_count: number
    total_products: number
  }
  sales_chart: ChartPoint[]
  low_stock_products: LowStockProduct[]
}>()

const chartMax = computed(() => Math.max(1, ...props.sales_chart.map(p => p.total)))
const barWidth = 40
const barGap = 16
const chartHeight = 160
const chartWidth = computed(() => props.sales_chart.length * (barWidth + barGap))

function barHeight(value: number): number {
  return Math.max(2, (value / chartMax.value) * (chartHeight - 30))
}
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h1 class="text-3xl font-bold">Dashboard</h1>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatsCard
          title="Today's sales"
          :value="stats.today_sales_total.toFixed(2)"
          :description="`${stats.today_sales_count} transactions`"
        />
        <StatsCard
          title="Low stock items"
          :value="stats.low_stock_count"
          description="Below minimum level"
        />
        <StatsCard
          title="Active products"
          :value="stats.total_products"
        />
        <StatsCard
          title="Pending services"
          value="—"
          description="Coming in Phase 3"
        />
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Sales — last 7 days</CardTitle>
          </CardHeader>
          <CardContent>
            <svg :width="chartWidth" :height="chartHeight" class="block">
              <g v-for="(point, i) in sales_chart" :key="point.date">
                <rect
                  :x="i * (barWidth + barGap) + 4"
                  :y="chartHeight - 24 - barHeight(point.total)"
                  :width="barWidth"
                  :height="barHeight(point.total)"
                  class="fill-primary"
                />
                <text
                  :x="i * (barWidth + barGap) + 4 + barWidth / 2"
                  :y="chartHeight - 8"
                  text-anchor="middle"
                  class="text-xs fill-muted-foreground"
                >{{ point.date }}</text>
                <text
                  :x="i * (barWidth + barGap) + 4 + barWidth / 2"
                  :y="chartHeight - 30 - barHeight(point.total)"
                  text-anchor="middle"
                  class="text-xs fill-foreground"
                >{{ point.total > 0 ? point.total.toFixed(0) : '' }}</text>
              </g>
            </svg>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Low stock</CardTitle>
          </CardHeader>
          <CardContent>
            <p v-if="low_stock_products.length === 0" class="text-muted-foreground text-sm">No low-stock items.</p>
            <ul v-else class="divide-y">
              <li v-for="p in low_stock_products" :key="p.id" class="py-2 flex items-center justify-between">
                <div>
                  <Link :href="`/app/products/${p.id}/edit`" class="font-medium hover:underline">{{ p.name }}</Link>
                  <div class="text-xs text-muted-foreground">{{ p.sku }} <span v-if="p.category">· {{ p.category }}</span></div>
                </div>
                <div class="text-right text-sm">
                  <span class="font-semibold text-destructive">{{ p.stock_quantity }}</span>
                  <span class="text-muted-foreground"> / min {{ p.min_stock_level }}</span>
                </div>
              </li>
            </ul>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
