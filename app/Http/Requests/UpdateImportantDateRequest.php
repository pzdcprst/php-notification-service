<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImportantDateRequest extends FormRequest
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
            'contact_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('contacts', 'id')->where('user_id', $this->user()->id),
            ],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'date' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'repeat_yearly' => ['sometimes', 'boolean'],
        ];
    }
}
