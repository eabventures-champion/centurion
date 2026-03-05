<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoundationProgressRequest extends FormRequest
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
            'foundation_class_id' => 'required|exists:foundation_classes,id',
            'first_timer_id' => 'nullable|exists:first_timers,id',
            'retained_member_id' => 'nullable|exists:retained_members,id',
            'completed' => 'required|boolean',
            'completion_date' => 'required_if:completed,1|date',
        ];
    }
}
