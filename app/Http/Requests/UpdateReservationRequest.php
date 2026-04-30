<?php

namespace App\Http\Requests;

use App\Services\ReservationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateReservationRequest extends FormRequest
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
            'salle_id' => 'sometimes|required|exists:salles,id',
            'date_debut' => 'sometimes|required|date|after:now',
            'date_fin' => 'sometimes|required|date|after:date_debut',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $reservation = $this->route('reservation');
            
            if ($reservation && $this->has(['salle_id', 'date_debut', 'date_fin'])) {
                try {
                    $this->reservationService->verifierConflit(
                        $this->salle_id,
                        $this->date_debut,
                        $this->date_fin,
                        $reservation->id
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
}