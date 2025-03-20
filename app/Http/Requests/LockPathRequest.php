<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LockPathRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cache_key' => 'required|string',
            'time_to_live' => ['required', 'string', 'regex:/^\d+(\.\d+)?\s+(seconds|minutes|hours|days)$/'],
        ];
    }
}
