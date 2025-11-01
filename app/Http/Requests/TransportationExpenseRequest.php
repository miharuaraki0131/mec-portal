<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransportationExpenseRequest extends FormRequest
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
            'period_from' => ['required', 'date'],
            'period_to' => ['required', 'date', 'after_or_equal:period_from'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.date' => ['required', 'date'],
            'items.*.business' => ['required', 'string', 'max:255'],
            'items.*.vehicle' => ['required', 'string', 'max:255'],
            'items.*.route_from' => ['required', 'string', 'max:255'],
            'items.*.route_via' => ['nullable', 'string', 'max:255'],
            'items.*.route_to' => ['required', 'string', 'max:255'],
            'items.*.transportation_type' => ['required', 'in:片道,往復'],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}

