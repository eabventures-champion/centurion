<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChurchGroupRequest extends FormRequest
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
            'church_category_id' => 'required|exists:church_categories,id',
            'group_name' => 'required|string|max:255',
            'pastor_name' => 'required|string|max:255',
            'pastor_contact' => [
                \Illuminate\Validation\Rule::requiredIf(function () {
                    $category = \App\Models\ChurchCategory::find($this->church_category_id);
                    $isZonalCategory = $category && $category->name === 'ZONAL CHURCH';
                    $isZonalGroupName = strtoupper($this->group_name) === 'ZONAL CHURCH GROUP 1';

                    return !$isZonalCategory && !$isZonalGroupName;
                }),
                'nullable',
                'string',
                new \App\Rules\UniqueContact(),
            ],
        ];
    }
}
