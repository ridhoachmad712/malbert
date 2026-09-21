<?php

namespace App\Http\Requests;

use App\Models\PointTransaction;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SavePointTransactionRequest extends FormRequest
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
            'activity_code' => ['required', 'in:'.implode(',', array_keys(PointTransaction::activities()))],
            'occurred_on' => ['required', 'date_format:Y-m-d'],
            'literacy_submission_id' => ['nullable', 'integer', 'exists:literacy_submissions,id'],
            'idempotency_key' => ['required', 'uuid'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
