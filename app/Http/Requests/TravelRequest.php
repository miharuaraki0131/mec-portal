<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TravelRequest extends FormRequest
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
            'destination' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:500'],
            'departure_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after:departure_date'],
            'advance_payment' => ['nullable', 'numeric', 'min:0'],
            'expenses' => ['required', 'array', 'min:1'],
            'expenses.*.date' => ['required', 'date'],
            'expenses.*.description' => ['required', 'string', 'max:255'],
            'expenses.*.category' => ['required', 'string', 'in:交通費,宿泊費,日当,半日当,その他'],
            'expenses.*.cash' => ['nullable', 'numeric', 'min:0'],
            'expenses.*.ticket' => ['nullable', 'numeric', 'min:0'],
            'expenses.*.remarks' => ['nullable', 'string', 'max:500'],
        ];
    }
}

