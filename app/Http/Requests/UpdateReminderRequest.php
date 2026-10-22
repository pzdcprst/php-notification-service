<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReminderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('events', 'id')->where('user_id', $this->user()->id),
            ],
            'remind_at' => ['sometimes', 'required', 'date'],
            'channel' => ['sometimes', 'required', Rule::in(['telegram', 'database'])],
        ];
    }
}
