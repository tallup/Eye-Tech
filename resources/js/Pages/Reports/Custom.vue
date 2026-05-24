<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import StatsCard from '@/components/StatsCard.vue'

interface ChartPoint { date: string; sales_count: number; revenue: number }
interface TopProduct { name: string; sku: string; quantity: number; revenue: number }
interface TopStaff { id: number; name: string; sales_count: number; revenue: number }
interface StatusRow { status: string; count: number; revenue: number }
interface PaymentRow { payment_method: string; count: number; revenue: number }

interface ReportPayload {
  totals: {
    sales_count: number
    revenue: number
    today_sales: number
    today_revenue: number
    week_sales: number
    week_revenue: number
    month_sales: number
    month_revenue: number
    avg_order_value: number
  }
  growth: { sales_pct: number; revenue_pct: number }
  daily_chart: ChartPoint[]
  top_products: TopProduct[]
  top_staff: TopStaff[]
  sales_by_status: StatusRow[]
  sales_by_payment: PaymentRow[]
  service_requests: { total: number; pending: number; in_progress: number; completed: number; conversion_rate: number }
  inventory: { total: number; active: number; low_stock: number; out_of_stock: number }
}

const props = defineProps<{ report: ReportPayload }>()

function money(v: number) {
  return new Intl.NumberFormat('en-GM', { style: 'currency', currency: 'GMD' }).format(v)
}

function pct(v: number) {
  return `${v.toFixed(1)}%`
}

const maxRevenue = Math.max(1, ...props.report.daily_chart.map((d) => d.revenue))
</script>

<template>
  <AppLayout>
    <div class="space-y-8">
      <h1 class="text-3xl font-bold">Custom Reports</h1>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <StatsCard title="Total sales" :value="report.totals.sales_count" :description="`Avg ${money(report.totals.avg_order_value)}`" />
        <StatsCard title="Total revenue" :value="money(report.totals.revenue)" :description="`Growth ${pct(report.growth.revenue_pct)}`" />
        <StatsCard title="Month sales" :value="report.totals.month_sales" :description="money(report.totals.month_revenue)" />
        <StatsCard title="Today" :value="report.totals.today_sales" :description="money(report.totals.today_revenue)" />
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div class="rounded-lg border bg-card p-6">
          <h2 class="text-lg font-semibold mb-3">Service requests</h2>
          <ul class="space-y-1 text-sm">
            <li class="flex justify-between"><span>Total</span><span>{{ report.service_requests.total }}</span></li>
            <li class="flex justify-between"><span>Pending</span><span>{{ report.service_requests.pending }}</span></li>
            <li class="flex justify-between"><span>In progress</span><span>{{ report.service_requests.in_progress }}</span></li>
            <li class="flex justify-between"><span>Completed</span><span>{{ report.service_requests.completed }}</span></li>
            <li class="flex justify-between border-t pt-2 font-medium"><span>Conversion rate</span><span>{{ pct(report.service_requests.conversion_rate) }}</span></li>
          </ul>
        </div>
        <div class="rounded-lg border bg-card p-6">
          <h2 class="text-lg font-semibold mb-3">Inventory</h2>
          <ul class="space-y-1 text-sm">
            <li class="flex justify-between"><span>Total products</span><span>{{ report.inventory.total }}</span></li>
            <li class="flex justify-between"><span>Active</span><span>{{ report.inventory.active }}</span></li>
            <li class="flex justify-between text-amber-700"><span>Low stock</span><span>{{ report.inventory.low_stock }}</span></li>
            <li class="flex justify-between text-red-700"><span>Out of stock</span><span>{{ report.inventory.out_of_stock }}</span></li>
          </ul>
        </div>
      </div>

      <div class="rounded-lg border bg-card p-6">
        <h2 class="text-lg font-semibold mb-4">Revenue last 30 days</h2>
        <div class="flex items-end gap-1 h-40">
          <div v-for="(d, i) in report.daily_chart" :key="i" class="flex-1 flex flex-col items-center gap-1">
            <div class="w-full bg-brand-red rounded-t" :style="{ height: `${(d.revenue / maxRevenue) * 100}%` }" :title="`${d.date}: ${money(d.revenue)}`" />
          </div>
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div class="rounded-lg border bg-card p-6">
          <h2 class="text-lg font-semibold mb-3">Top products</h2>
          <table class="w-full text-sm">
            <thead class="text-left text-xs text-muted-foreground border-b">
              <tr><th class="py-2">Product</th><th>Qty</th><th class="text-right">Revenue</th></tr>
            </thead>
            <tbody>
              <tr v-for="p in report.top_products" :key="p.sku" class="border-b last:border-0">
                <td class="py-2">{{ p.name }} <span class="text-xs text-muted-foreground">{{ p.sku }}</span></td>
                <td>{{ p.quantity }}</td>
                <td class="text-right">{{ money(p.revenue) }}</td>
              </tr>
              <tr v-if="!report.top_products.length"><td colspan="3" class="text-muted-foreground py-3">No sales yet</td></tr>
            </tbody>
          </table>
        </div>
        <div class="rounded-lg border bg-card p-6">
          <h2 class="text-lg font-semibold mb-3">Top staff</h2>
          <table class="w-full text-sm">
            <thead class="text-left text-xs text-muted-foreground border-b">
              <tr><th class="py-2">Cashier</th><th>Sales</th><th class="text-right">Revenue</th></tr>
            </thead>
            <tbody>
              <tr v-for="s in report.top_staff" :key="s.id" class="border-b last:border-0">
                <td class="py-2">{{ s.name }}</td>
                <td>{{ s.sales_count }}</td>
                <td class="text-right">{{ money(s.revenue) }}</td>
              </tr>
              <tr v-if="!report.top_staff.length"><td colspan="3" class="text-muted-foreground py-3">No staff sales yet</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
