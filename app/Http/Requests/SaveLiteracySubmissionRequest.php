<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SaveLiteracySubmissionRequest extends FormRequest
{
    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $file = $this->file('work_file');
            if (! $file) {
                return;
            }
            $mime = $file->getMimeType();
            if ($this->input('work_type') === 'photo' && ! str_starts_with((string) $mime, 'image/')) {
                $validator->errors()->add('work_file', 'Jenis karya foto harus menggunakan berkas gambar.');
            }
            if ($this->input('work_type') === 'pdf' && $mime !== 'application/pdf') {
                $validator->errors()->add('work_file', 'Jenis karya PDF harus menggunakan dokumen PDF.');
            }
        }];
    }

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
        $required = $this->input('intent') === 'submit' ? 'required' : 'nullable';

        return [
            'journal_date' => [$required, 'date_format:Y-m-d'],
            'duration_minutes' => ['nullable', 'integer', 'between:1,1440'],
            'summary' => [$required, 'string', 'max:10000'],
            'work_type' => ['required', 'in:none,text,link,photo,pdf'],
            'work_text' => [$this->input('work_type') === 'text' && $required === 'required' ? 'required' : 'nullable', 'string', 'max:30000'],
            'work_url' => [$this->input('work_type') === 'link' && $required === 'required' ? 'required' : 'nullable', 'url:https', 'max:1000'],
            'work_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'reflection_understanding' => [$required, 'string', 'max:5000'],
            'reflection_feeling' => [$required, 'string', 'max:5000'],
            'reflection_commitment' => [$required, 'string', 'max:5000'],
            'intent' => ['required', 'in:draft,submit'],
        ];
    }
}
