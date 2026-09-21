<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveAssessmentSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->active === true && $this->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'score_rounding' => ['required', 'in:whole,one_decimal'],
            'success_threshold' => ['required', 'numeric', 'between:0,100'],
            'point_period' => ['required', 'in:month,semester,academic_year'],
            'repeat_rule' => ['required', 'in:once_daily,distinct_event'],
            'monitoring_period' => ['required', 'in:month,semester,academic_year'],
            'student_score_method' => ['required', 'in:average,latest'],
            'achievement_population' => ['required', 'in:enrolled,participants'],
            'unassessed_rule' => ['required', 'in:not_successful,exclude'],
        ];
    }
}
