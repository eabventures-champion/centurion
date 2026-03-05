<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePcfRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'church_group_id' => 'required|exists:church_groups,id',
            'name' => 'required|string|max:255',
            'leader_name' => 'required|string|max:255',
            'leader_contact' => ['required', 'string', 'max:20', new \App\Rules\UniqueContact()],
            'official_id' => 'required|exists:users,id',
            'gender' => 'required|in:Male,Female',
            'marital_status' => 'required|in:Single,Married,Divorced,Widowed',
            'occupation' => 'required|string|max:255',
        ];
    }
}
