import type { User } from './models'

declare module '@inertiajs/core' {
  interface PageProps {
    auth: { user: User | null }
    flash: {
      success: string | null
      error: string | null
      print: boolean | null
    }
    app: { name: string; version: string }
  }
}

export {}
