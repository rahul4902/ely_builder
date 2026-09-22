<?php

namespace App\Http\Requests\Lead;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', \App\Models\Lead::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => '',
            'name' => 'required',
            'email' =>'',
            'source'  => 'required',
            'project' => 'required',
            'Budget'   =>'',
            'location' => 'required',
            'requirement' =>'required',
            'contact_no ' =>'',
            'note' => '',
            'country' =>'',
            'state' => '',
            'city'=>'',
            'pin'=> '',
            'status' => 'required',
            'user_assigned_id' => 'required',
            'user_created_id' => '',
            'client_id' => '',
            'contact_date' => 'required'
        ];
    }
}
