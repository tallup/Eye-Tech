<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('category')) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->slug && $this->name) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,'.$this->route('category')->id],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
        ];
    }
}
