<?php

namespace App\Http\Requests\PersonalTasks;

use App\Models\PersonalTask;
use App\Support\RichTextSanitizer;
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
        $isCompleted = filter_var($this->input('is_completed'), FILTER_VALIDATE_BOOL);
        $status = PersonalTask::normalizeStatus((string) $this->input('status'));

        $this->merge([
            'title' => trim((string) $this->input('title')),
            'description' => RichTextSanitizer::sanitize($this->input('description')),
            'status' => $isCompleted ? 'completada' : $status,
            'priority' => PersonalTask::normalizePriority((string) $this->input('priority')),
            'due_date' => $this->filled('due_date') ? $this->input('due_date') : null,
            'completed_at' => $isCompleted ? ($this->personalTask?->completed_at ?? now()) : null,
            'is_completed' => $isCompleted,
            'order' => $this->filled('order') ? $this->input('order') : null,
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
            'status' => ['required', 'string', Rule::in(PersonalTask::STATUSES)],
            'priority' => ['nullable', 'string', Rule::in(PersonalTask::PRIORITIES)],
            'due_date' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'is_completed' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
