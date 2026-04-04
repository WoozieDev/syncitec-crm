<?php

namespace App\Http\Requests\PersonalTasks;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonalTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'description' => $this->filled('description') ? trim((string) $this->input('description')) : null,
            'status' => trim((string) $this->input('status')),
            'priority' => $this->filled('priority') ? trim((string) $this->input('priority')) : null,
            'order' => $this->filled('order') ? $this->input('order') : 0,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in([
                'pendiente',
                'en_progreso',
                'en_revision',
                'completada',
            ])],
            'priority' => ['nullable', 'string', Rule::in([
                'alta',
                'media',
                'baja',
            ])],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
