<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveLiteracyAssessmentRequest extends FormRequest
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
            'focus_score' => ['required', 'integer', 'between:1,4'],
            'engagement_score' => ['required', 'integer', 'between:1,4'],
            'answer_score' => ['required', 'integer', 'between:1,4'],
            'retell_score' => ['required', 'integer', 'between:1,4'],
            'main_idea_score' => ['required', 'integer', 'between:1,4'],
            'summary_work_score' => ['required', 'integer', 'between:1,4'],
            'feedback' => ['nullable', 'string', 'max:5000'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
