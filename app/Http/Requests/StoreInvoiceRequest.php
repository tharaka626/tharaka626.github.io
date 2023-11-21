<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
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
            'customer_id' => 'required',
            'date' => 'date',
            'number' => 'required',
            'reference' => 'nullable',
            'discount' => 'required|numeric',
            'sub_total' => 'required|numeric',
            'total' => 'required|numeric',
            'terms_and_conditions' => 'nullable',
            'invoice_item' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'The customer field is required',
        ];
    }
}
