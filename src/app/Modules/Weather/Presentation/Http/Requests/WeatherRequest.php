<?php

namespace App\Modules\Weather\Presentation\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class WeatherRequest extends FormRequest
{
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
            'city' => ['required', 'string', 'min:2', 'max:50'],
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
            'city.required' => 'City name is required',
            'city.string' => 'City name must be a string',
            'city.min' => 'City name must be at least :min characters',
            'city.max' => 'City name cannot exceed :max characters',
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
     * @return array{city: string}
     */
    public function validatedTyped(): array
    {
        /** @var array{city: string} $data*/
        $data = $this->validated();
        return $data;
    }
}
