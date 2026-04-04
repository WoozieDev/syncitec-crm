<?php

namespace App\Http\Requests\Services;

use App\Models\Service;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
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
            'client_id' => $this->filled('client_id') ? $this->input('client_id') : null,
            'service_type_id' => $this->filled('service_type_id') ? $this->input('service_type_id') : null,
            'provider_id' => $this->filled('provider_id') ? $this->input('provider_id') : null,
            'name' => trim((string) $this->input('name')),
            'description' => $this->filled('description') ? trim((string) $this->input('description')) : null,
            'billing_type' => trim((string) $this->input('billing_type')),
            'billing_cycle' => $this->filled('billing_cycle') ? trim((string) $this->input('billing_cycle')) : null,
            'price' => $this->filled('price') ? $this->input('price') : null,
            'cost' => $this->filled('cost') ? $this->input('cost') : null,
            'start_date' => $this->filled('start_date') ? $this->input('start_date') : null,
            'next_renewal_date' => $this->filled('next_renewal_date') ? $this->input('next_renewal_date') : null,
            'status' => trim((string) $this->input('status')),
            'notes' => $this->filled('notes') ? trim((string) $this->input('notes')) : null,
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
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'service_type_id' => ['required', 'integer', 'exists:service_types,id'],
            'provider_id' => ['nullable', 'integer', 'exists:providers,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'billing_type' => ['required', Rule::in(Service::BILLING_TYPES)],
            'billing_cycle' => [
                'nullable',
                'required_if:billing_type,recurrente',
                'string',
                Rule::in(Service::BILLING_CYCLES),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'next_renewal_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', Rule::in(Service::STATUSES)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
