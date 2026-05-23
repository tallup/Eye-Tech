import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import type { User } from '@/types/models'

export function useAuth() {
  const page = usePage()
  const user = computed<User | null>(() => (page.props.auth as { user: User | null }).user)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isCashier = computed(() => user.value?.role === 'cashier')
  return { user, isAdmin, isCashier }
}
