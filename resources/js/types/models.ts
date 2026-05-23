export type Role = 'admin' | 'cashier'

export interface User {
  id: number
  name: string
  email: string
  role: Role
}
