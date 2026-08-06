<script setup lang="ts" generic="T">
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table'
import { Link } from '@inertiajs/vue3'

interface PaginatedResponse<T> {
  data: T[]
  links: { url: string | null; label: string; active: boolean }[]
  meta: { current_page: number; last_page: number; total: number }
}

interface Column<T> {
  key: string
  label: string
  render?: (row: T) => string
}

defineProps<{
  data: PaginatedResponse<T>
  columns: Column<T>[]
  rowLink?: (row: T) => string
}>()

function navigateTo(url: string) {
  window.location.href = url
}
</script>

<template>
  <div class="space-y-4">
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead v-for="col in columns" :key="col.key">{{ col.label }}</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow
          v-for="row in data.data"
          :key="(row as any).id"
          :class="rowLink ? 'cursor-pointer hover:bg-muted/40' : ''"
          @click="rowLink && navigateTo(rowLink(row))"
        >
          <TableCell v-for="col in columns" :key="col.key">
            <slot :name="`cell-${col.key}`" :row="row">
              {{ col.render ? col.render(row) : (row as any)[col.key] }}
            </slot>
          </TableCell>
        </TableRow>
        <TableRow v-if="data.data.length === 0">
          <TableCell :colspan="columns.length" class="text-center text-muted-foreground py-8">
            No records.
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
    <div v-if="data.meta && data.meta.last_page > 1" class="flex gap-2 justify-center">
      <Link
        v-for="link in data.links"
        :key="link.label"
        :href="link.url ?? '#'"
        v-html="link.label"
        :class="['px-3 py-1 rounded border text-sm', link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted', !link.url && 'opacity-50 pointer-events-none']"
      />
    </div>
  </div>
</template>
