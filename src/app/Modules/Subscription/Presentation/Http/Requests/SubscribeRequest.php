<?php

namespace App\Modules\Subscription\Presentation\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class SubscribeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int|string, string>|Rule[]>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns', 'min:5', 'max:254'],
            'city' => ['required', 'string', 'min:2', 'max:50'],
            'frequency' => ['required', Rule::in(['daily', 'hourly'])],
        ];
    }

    /**
     * Custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required',
            'email.email' => 'Please provide a valid email address',
            'email.min' => 'Email must be at least :min characters',
            'email.max' => 'Email cannot exceed :max characters',

            'city.required' => 'City name is required',
            'city.string' => 'City name must be a string',
            'city.min' => 'City name must be at least :min characters',
            'city.max' => 'City name cannot exceed :max characters',

            'frequency.required' => 'Weather update frequency is required',
            'frequency.in' => 'Frequency must be either "daily" or "hourly"',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST)
        );
    }

    /**
     * Get validated data with strict types.
     *
     * @return array{email: string, city: string, frequency: string}
     */
    public function validatedTyped(): array
    {
        /** @var array{email: string, city: string, frequency: string} $data */
        $data = $this->validated();
        return $data;
    }
}
