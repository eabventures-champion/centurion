<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChurchRequest extends FormRequest
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
            'title' => 'required|in:Bro,Sis,Pastor,Dcn,Dcns,Mr,Mrs',
            'leader_name' => 'required|string|max:255',
            'leader_contact' => ['required', 'string', new \App\Rules\UniqueContact()],
            'location' => 'nullable|string|max:255',
        ];
    }
}
