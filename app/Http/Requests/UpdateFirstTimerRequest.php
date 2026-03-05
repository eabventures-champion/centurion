<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFirstTimerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $firstTimerId = $this->route('first_timer')->id;

        return [
            'church_id' => 'required_without:pcf_id|nullable|exists:churches,id',
            'pcf_id' => 'required_without:church_id|nullable|exists:pcfs,id',
            'email' => 'nullable|email|max:255',
            'full_name' => 'required|string|max:255',
            'primary_contact' => [
                'required',
                'string',
                'max:20',
                new \App\Rules\UniqueContact($firstTimerId, 'visitor'),
            ],
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
            'is_self_brought' => 'nullable|boolean',
            'new_bringer' => 'nullable|array',
            'new_bringer.name' => 'required_with:new_bringer|string|max:255',
            'new_bringer.contact' => ['required_with:new_bringer', 'string', 'max:20', new \App\Rules\UniqueContact()],
            'new_bringer.senior_cell_name' => 'nullable|string|max:255',
            'new_bringer.cell_name' => 'nullable|string|max:255',
        ];
    }
}
