<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

class SavePublicationRequest extends FormRequest
{
    public function after(): array
    {
        return [function (Validator $validator): void {
            $bytes = 0;
            foreach (Arr::flatten($this->allFiles()) as $file) {
                if ($file instanceof UploadedFile) {
                    $bytes += $file->getSize();
                }
            }
            if ($bytes > 30 * 1024 * 1024) {
                $validator->errors()->add('photos', 'Total gambar maksimal 30 MB per penyimpanan. Unggah sisanya setelah draf disimpan.');
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
        return [
            'drive_folder' => ['nullable', 'string', 'max:1000'],
            'title' => ['required', 'string', 'max:160'],
            'type' => ['required', 'in:kegiatan,karya,prestasi'],
            'summary' => ['required', 'string', 'max:300'],
            'body' => ['required', 'string', 'max:30000'],
            'event_date' => ['required', 'date'],
            'school_year' => ['required', 'regex:/^20\d{2}\/20\d{2}$/'],
            'classes' => ['nullable', 'array', 'max:6'],
            'classes.*' => ['integer', 'between:1,6'],
            'achievement' => ['nullable', 'string', 'max:300'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240', 'dimensions:max_width=8000,max_height=8000'],
            'photos' => ['nullable', 'array', 'max:12'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240', 'dimensions:max_width=8000,max_height=8000'],
            'media' => ['nullable', 'array', 'max:30'],
            'media.*.url' => ['nullable', 'string', 'max:1000'],
            'media.*.kind' => ['required', 'in:photo,video'],
            'media.*.caption' => ['nullable', 'string', 'max:300'],
            'existing' => ['nullable', 'array', 'max:30'],
            'existing.*.path' => ['required', 'string'],
            'existing.*.caption' => ['nullable', 'string', 'max:300'],
            'intent' => ['required', 'in:draft,submit'],
            'permission_confirmed' => ['accepted'],
        ];
    }
}
