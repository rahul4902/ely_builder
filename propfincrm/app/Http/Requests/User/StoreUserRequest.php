<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', \App\Models\User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'address' => '',
            'work_number' => 'numeric',
            'personal_number' => 'numeric',
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required|min:5',
            'image_path' => '',
            'roles' => 'required',
            'departments' => 'required',
            'teamlead' => 'nullable|exists:users,id',
        ];
    }
}
