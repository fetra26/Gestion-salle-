<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255|unique:salles,nom,' . $this->salle->id,
            'capacite' => 'required|integer|min:1|max:500',
            'description' => 'nullable|string|max:1000',
            'equipement' => 'nullable|string|max:1000',
            'active' => 'nullable|boolean',
        ];
    }
}