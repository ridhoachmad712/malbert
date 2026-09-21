<?php

namespace App\Http\Requests;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $student = $this->route('student');
        if ($student) {
            abort_unless(Student::visibleTo($this->user())->whereKey($student->id)->exists(), 404);
        } elseif (filter_var($this->input('classroom_id'), FILTER_VALIDATE_INT) !== false) {
            abort_unless(Classroom::visibleTo($this->user())->whereKey($this->input('classroom_id'))->exists(), 404);
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $student = $this->route('student');

        return array_merge(self::identityRules($student), [
            'classroom_id' => [$student ? 'prohibited' : 'required', 'integer', 'exists:classrooms,id'],
            'starts_on' => [$student ? 'prohibited' : 'required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'active' => [$this->user()->role === 'admin' ? 'required' : 'prohibited', 'boolean'],
            'verified' => [$this->user()->role === 'admin' ? 'required' : 'prohibited', 'boolean'],
        ]);
    }

    /** @return array<string, array<mixed>|string> */
    public static function identityRules(?Student $student = null): array
    {
        return [
            'name' => 'required|string|max:120',
            'nis' => ['nullable', 'required_without:nisn', 'string', 'regex:/^[0-9]{1,30}$/', Rule::unique('students')->ignore($student?->id)],
            'nisn' => ['nullable', 'required_without:nis', 'string', 'regex:/^[0-9]{10}$/', Rule::unique('students')->ignore($student?->id)],
        ];
    }
}
