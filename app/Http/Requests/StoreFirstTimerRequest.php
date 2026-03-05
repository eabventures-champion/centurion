<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFirstTimerRequest extends FormRequest
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
        $isSuperAdmin = $this->user()->hasRole('Super Admin');

        return [
            'church_id' => $isSuperAdmin ? 'required_without:pcf_id|nullable|exists:churches,id' : 'required|exists:churches,id',
            'pcf_id' => $isSuperAdmin ? 'required_without:church_id|nullable|exists:pcfs,id' : 'nullable|exists:pcfs,id',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'primary_contact' => ['required', 'string', 'max:20', new \App\Rules\UniqueContact()],
            'alternate_contact' => 'nullable|string|max:20',
            'gender' => 'required|in:Male,Female',
            'birth_day_day' => 'nullable|string|max:2',
            'birth_day_month' => 'nullable|string|max:2',
            'date_of_birth' => 'nullable|string',
            'residential_address' => 'required|string',
            'date_of_visit' => 'required|date',
            'occupation' => 'nullable|string|max:255',
            'marital_status' => 'required|in:Single,Married,Widowed,Divorced',
            'born_again' => 'nullable|boolean',
            'water_baptism' => 'nullable|boolean',
            'prayer_requests' => 'nullable|string',
            'bringer_id' => 'nullable|exists:bringers,id',
            'new_bringer' => 'nullable|array',
            'new_bringer.name' => 'required_with:new_bringer|string|max:255',
            'new_bringer.contact' => ['required_with:new_bringer', 'string', 'max:20', new \App\Rules\UniqueContact()],
            'new_bringer.senior_cell_name' => 'nullable|string|max:255',
            'new_bringer.cell_name' => 'nullable|string|max:255',
        ];
    }
}
