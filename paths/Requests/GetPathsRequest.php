<?php

namespace Paths\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetPathsRequest extends FormRequest
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
            'source' => ['required', Rule::in(config('constants.CITIES'))],
            'destination' => ['required', Rule::in(config('constants.CITIES'))],
        ];
    }

    public function messages(): array {
        return [
            'source.in' => 'Invalid source city, check for typos',
            'destination.in' => 'Invalid destination city, check for typos',
        ];
    }
}
