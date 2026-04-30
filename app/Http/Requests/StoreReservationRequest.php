<?php

namespace App\Http\Requests;

use App\Services\ReservationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReservationRequest extends FormRequest
{
    public function __construct(
        private ReservationService $reservationService
    ) {}

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'salle_id' => 'required|exists:salles,id',
            'date_debut' => 'required|date|after:now',
            'date_fin' => 'required|date|after:date_debut',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has(['salle_id', 'date_debut', 'date_fin'])) {
                try {
                    $this->reservationService->verifierConflit(
                        $this->salle_id,
                        $this->date_debut,
                        $this->date_fin
                    );
                } catch (\Illuminate\Validation\ValidationException $e) {
                    foreach ($e->errors() as $errors) {
                        foreach ($errors as $error) {
                            $validator->errors()->add('reservation', $error);
                        }
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'salle_id.required' => 'Veuillez sélectionner une salle.',
            'salle_id.exists' => 'La salle sélectionnée n\'existe pas.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.after' => 'La date de début doit être dans le futur.',
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.after' => 'La date de fin doit être après la date de début.',
        ];
    }
}