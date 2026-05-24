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

const maxRevenue = Math.max(1, ...props.report.daily_chart.map((d) => d.revenue))
</script>

<template>
  <AppLayout>
    <div class="space-y-8">
      <h1 class="text-3xl font-bold">Reports</h1>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <StatsCard title="Today's sales" :value="report.totals.today_sales" :description="money(report.totals.today_revenue)" />
        <StatsCard title="This week" :value="report.totals.week_sales" :description="money(report.totals.week_revenue)" />
        <StatsCard title="This month" :value="report.totals.month_sales" :description="money(report.totals.month_revenue)" />
        <StatsCard title="All time revenue" :value="money(report.totals.revenue)" :description="`Avg ${money(report.totals.avg_order_value)}`" />
      </div>

      <div class="rounded-lg border bg-card p-6">
        <h2 class="text-lg font-semibold mb-4">Revenue last 7 days</h2>
        <div class="flex items-end gap-2 h-40">
          <div v-for="(d, i) in report.daily_chart" :key="i" class="flex-1 flex flex-col items-center gap-1">
            <div class="w-full bg-brand-red rounded-t" :style="{ height: `${(d.revenue / maxRevenue) * 100}%` }" :title="money(d.revenue)" />
            <span class="text-[10px] text-muted-foreground">{{ d.date }}</span>
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

      <div class="grid md:grid-cols-2 gap-6">
        <div class="rounded-lg border bg-card p-6">
          <h2 class="text-lg font-semibold mb-3">Sales by status</h2>
          <ul class="space-y-1 text-sm">
            <li v-for="r in report.sales_by_status" :key="r.status" class="flex justify-between">
              <span class="capitalize">{{ r.status }}</span>
              <span>{{ r.count }} — {{ money(r.revenue) }}</span>
            </li>
            <li v-if="!report.sales_by_status.length" class="text-muted-foreground">No sales</li>
          </ul>
        </div>
        <div class="rounded-lg border bg-card p-6">
          <h2 class="text-lg font-semibold mb-3">Sales by payment</h2>
          <ul class="space-y-1 text-sm">
            <li v-for="r in report.sales_by_payment" :key="r.payment_method" class="flex justify-between">
              <span class="capitalize">{{ r.payment_method.replace('_', ' ') }}</span>
              <span>{{ r.count }} — {{ money(r.revenue) }}</span>
            </li>
            <li v-if="!report.sales_by_payment.length" class="text-muted-foreground">No sales</li>
          </ul>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
