import { useForm as inertiaUseForm } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'

type FormDataType = Record<string, any>

// Re-export Inertia's useForm directly — pages use it as-is.
export const useForm = inertiaUseForm

// Opinionated helper: submit a form via the given HTTP method, toast on success/error.
export function submitWithToast<T extends FormDataType>(
  form: ReturnType<typeof inertiaUseForm<T>>,
  method: 'post' | 'put' | 'patch' | 'delete',
  url: string,
  successMessage = 'Saved',
) {
  form[method](url, {
    onSuccess: () => toast.success(successMessage),
    onError: () => toast.error('Please check the form for errors.'),
  })
}
