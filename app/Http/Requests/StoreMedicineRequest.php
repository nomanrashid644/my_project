<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Medicine::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'medicine_name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:medicine_categories,id'],
            'generic_name' => ['nullable', 'string', 'max:150'],
            'strength' => ['nullable', 'string', 'max:100'],
            'dosage_form' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'gt:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'requires_prescription' => ['boolean'],
        ];
    }
}