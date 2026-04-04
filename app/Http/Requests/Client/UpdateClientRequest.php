<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
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
			'name' => trim((string) $this->name),
			'email' => trim(strtolower((string) $this->email)),
			'company' => $this->company !== null ? trim((string) $this->company) : null,
			'phone' => $this->phone !== null ? trim((string) $this->phone) : null,
			'country' => $this->country !== null ? trim((string) $this->country) : null,
			'notes' => $this->notes !== null ? trim((string) $this->notes) : null,
		]);
	}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var \App\Models\Client $client */
		$client = $this->route('client');

        return [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clients', 'email')->ignore($client),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
