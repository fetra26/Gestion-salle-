<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255|unique:salles,nom',
            'capacite' => 'required|integer|min:1|max:500',
            'description' => 'nullable|string|max:1000',
            'equipement' => 'nullable|string|max:1000',
            'active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de la salle est obligatoire.',
            'nom.unique' => 'Une salle avec ce nom existe déjà.',
            'capacite.required' => 'La capacité est obligatoire.',
            'capacite.min' => 'La capacité doit être au moins de 1 personne.',
            'capacite.max' => 'La capacité ne peut pas dépasser 500 personnes.',
        ];
    }
}