<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Controllerで制御
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $action = $this->route()->getActionMethod();
        
        if ($action === 'reject') {
            return [
                'comment' => ['required', 'string', 'max:1000'],
            ];
        }
        
        return [
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

