<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

interface Profile {
  id: number
  name: string
  email: string
  phone: string | null
  profile_picture_url: string | null
}

const props = defineProps<{ profile: Profile }>()

const infoForm = useForm({
  name: props.profile.name,
  email: props.profile.email,
  phone: props.profile.phone ?? '',
  profile_picture: null as File | null,
  _method: 'put',
})

const passwordForm = useForm({
  name: props.profile.name,
  email: props.profile.email,
  phone: props.profile.phone ?? '',
  current_password: '',
  password: '',
  password_confirmation: '',
  _method: 'put',
})

const fileInput = ref<HTMLInputElement | null>(null)

function pickFile(e: Event) {
  const target = e.target as HTMLInputElement
  infoForm.profile_picture = target.files?.[0] ?? null
}

function saveInfo() {
  infoForm.post('/app/profile', {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Profile updated')
      infoForm.profile_picture = null
      if (fileInput.value) fileInput.value.value = ''
    },
  })
}

function savePassword() {
  passwordForm.post('/app/profile', {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Password updated')
      passwordForm.reset('current_password', 'password', 'password_confirmation')
    },
  })
}
</script>

<template>
  <AppLayout>
    <div class="space-y-8 max-w-2xl">
      <h1 class="text-3xl font-bold">Profile</h1>

      <form @submit.prevent="saveInfo" class="space-y-4 rounded-lg border bg-card p-6">
        <h2 class="text-lg font-semibold">Account info</h2>

        <div v-if="profile.profile_picture_url" class="flex items-center gap-4">
          <img :src="profile.profile_picture_url" alt="" class="h-16 w-16 rounded-full object-cover" />
          <span class="text-sm text-muted-foreground">Current photo</span>
        </div>

        <div class="space-y-2">
          <Label html-for="name">Name</Label>
          <Input id="name" v-model="infoForm.name" />
          <p v-if="infoForm.errors.name" class="text-sm text-destructive">{{ infoForm.errors.name }}</p>
        </div>

        <div class="space-y-2">
          <Label html-for="email">Email</Label>
          <Input id="email" type="email" v-model="infoForm.email" />
          <p v-if="infoForm.errors.email" class="text-sm text-destructive">{{ infoForm.errors.email }}</p>
        </div>

        <div class="space-y-2">
          <Label html-for="phone">Phone</Label>
          <Input id="phone" v-model="infoForm.phone" />
        </div>

        <div class="space-y-2">
          <Label html-for="profile_picture">Profile picture</Label>
          <input
            id="profile_picture"
            ref="fileInput"
            type="file"
            accept="image/*"
            class="block text-sm"
            @change="pickFile"
          />
          <p v-if="infoForm.errors.profile_picture" class="text-sm text-destructive">{{ infoForm.errors.profile_picture }}</p>
        </div>

        <Button type="submit" :disabled="infoForm.processing">{{ infoForm.processing ? 'Saving…' : 'Save' }}</Button>
      </form>

      <form @submit.prevent="savePassword" class="space-y-4 rounded-lg border bg-card p-6">
        <h2 class="text-lg font-semibold">Change password</h2>

        <div class="space-y-2">
          <Label html-for="current_password">Current password</Label>
          <Input id="current_password" type="password" v-model="passwordForm.current_password" />
          <p v-if="passwordForm.errors.current_password" class="text-sm text-destructive">{{ passwordForm.errors.current_password }}</p>
        </div>

        <div class="space-y-2">
          <Label html-for="password">New password</Label>
          <Input id="password" type="password" v-model="passwordForm.password" />
          <p v-if="passwordForm.errors.password" class="text-sm text-destructive">{{ passwordForm.errors.password }}</p>
        </div>

        <div class="space-y-2">
          <Label html-for="password_confirmation">Confirm new password</Label>
          <Input id="password_confirmation" type="password" v-model="passwordForm.password_confirmation" />
        </div>

        <Button type="submit" :disabled="passwordForm.processing">{{ passwordForm.processing ? 'Updating…' : 'Update password' }}</Button>
      </form>
    </div>
  </AppLayout>
</template>
