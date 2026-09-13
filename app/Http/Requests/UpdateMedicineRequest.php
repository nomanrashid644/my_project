<?php

namespace App\Http\Requests;

class UpdateMedicineRequest extends StoreMedicineRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('medicine')) ?? false;
    }
}