<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post('/login', { onFinish: () => form.reset('password') })
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-muted/40 px-4">
    <Card class="w-full max-w-md">
      <CardHeader>
        <CardTitle>Sign in</CardTitle>
        <CardDescription>EyeTech admin &amp; POS</CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit" class="space-y-4">
          <div class="space-y-2">
            <Label for="email">Email</Label>
            <Input
              id="email"
              type="email"
              autocomplete="email"
              v-model="form.email"
              :disabled="form.processing"
            />
            <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
          </div>
          <div class="space-y-2">
            <Label for="password">Password</Label>
            <Input
              id="password"
              type="password"
              autocomplete="current-password"
              v-model="form.password"
              :disabled="form.processing"
            />
            <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
          </div>
          <Button type="submit" :disabled="form.processing" class="w-full">
            Sign in
          </Button>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
