<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'mobile'      => 'required_without:email|string|max:50',
            'email'       => 'required_without:mobile|nullable|email|max:255',
            'password'    => 'nullable|string|min:4',
            'pushtoken'   => 'nullable|string|max:500',
            'company_id'  => 'nullable|integer|exists:companies,id',
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'mobile.required_without' => 'Please provide a mobile number or email address.',
            'email.required_without'  => 'Please provide a mobile number or email address.',
            'company_id.exists'       => 'The selected company workspace does not exist.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
