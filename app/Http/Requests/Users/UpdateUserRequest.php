<?php

namespace App\Http\Requests\Users;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $password = $this->input('password');
        $passwordConfirmation = $this->input('password_confirmation');

        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => trim(strtolower((string) $this->input('email'))),
            'password' => is_string($password) && trim($password) === '' ? null : $password,
            'password_confirmation' => is_string($passwordConfirmation) && trim($passwordConfirmation) === ''
                ? null
                : $passwordConfirmation,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            ...$this->profileRules($user->id),
            'password' => ['nullable', 'string', Password::default(), 'confirmed'],
        ];
    }
}
