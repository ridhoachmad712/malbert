<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveLiteracyAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->active === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'classroom_id' => [$this->route('literacyAssignment') ? 'prohibited' : 'required', 'integer', 'exists:classrooms,id'],
            'reading_resource_id' => ['nullable', 'integer', 'exists:reading_resources,id'],
            'title' => ['required', 'string', 'max:160'],
            'instructions' => ['required', 'string', 'max:10000'],
            'assigned_on' => ['required', 'date_format:Y-m-d'],
            'due_at' => ['nullable', 'date', 'after_or_equal:assigned_on'],
            'status' => ['required', 'in:draft,active,closed'],
        ];
    }
}
