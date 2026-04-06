<?php

namespace App\Http\Requests\ProjectTasks;

use App\Models\Task;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectTaskRequest extends FormRequest
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
            'project_id' => $this->filled('project_id') ? $this->input('project_id') : null,
            'module_id' => $this->filled('module_id') ? $this->input('module_id') : null,
            'title' => trim((string) $this->input('title')),
            'description' => $this->filled('description') ? trim((string) $this->input('description')) : null,
            'status' => Task::canonicalStatus((string) $this->input('status'))
                ?? trim((string) $this->input('status')),
            'priority' => $this->filled('priority')
                ? (Task::canonicalPriority((string) $this->input('priority'))
                    ?? trim((string) $this->input('priority')))
                : null,
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
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'module_id' => [
                'required',
                'integer',
                Rule::exists('project_modules', 'id')->where(function ($query) {
                    if ($this->filled('project_id')) {
                        $query->where('project_id', $this->input('project_id'));
                    }
                }),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(Task::STATUSES)],
            'priority' => ['nullable', 'string', Rule::in(Task::PRIORITIES)],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
