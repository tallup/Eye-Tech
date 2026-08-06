import { describe, expect, it, vi } from 'vitest'
import { useAuth } from '@/composables/useAuth'

vi.mock('@inertiajs/vue3', () => ({
  usePage: vi.fn(),
}))

import { usePage } from '@inertiajs/vue3'

describe('useAuth', () => {
  it('returns isAdmin=true when user role is admin', () => {
    ;(usePage as any).mockReturnValue({
      props: { auth: { user: { id: 1, name: 'A', email: 'a@x', role: 'admin' } } },
    })
    const { user, isAdmin, isCashier } = useAuth()
    expect(user.value?.role).toBe('admin')
    expect(isAdmin.value).toBe(true)
    expect(isCashier.value).toBe(false)
  })

  it('returns isCashier=true when user role is cashier', () => {
    ;(usePage as any).mockReturnValue({
      props: { auth: { user: { id: 2, name: 'B', email: 'b@x', role: 'cashier' } } },
    })
    const { isAdmin, isCashier } = useAuth()
    expect(isAdmin.value).toBe(false)
    expect(isCashier.value).toBe(true)
  })

  it('returns null user when unauthenticated', () => {
    ;(usePage as any).mockReturnValue({ props: { auth: { user: null } } })
    const { user, isAdmin } = useAuth()
    expect(user.value).toBeNull()
    expect(isAdmin.value).toBe(false)
  })
})
