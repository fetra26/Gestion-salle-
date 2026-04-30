@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Modifier la salle</h1>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('salles.update', $salle) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom de la salle *</label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" name="nom" value="{{ old('nom', $salle->nom) }}" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="capacite" class="form-label">Capacité *</label>
                        <input type="number" class="form-control @error('capacite') is-invalid @enderror" 
                               id="capacite" name="capacite" value="{{ old('capacite', $salle->capacite) }}" min="1" max="500" required>
                        @error('capacite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $salle->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="equipement" class="form-label">Équipement</label>
                    <textarea class="form-control @error('equipement') is-invalid @enderror" 
                              id="equipement" name="equipement" rows="3">{{ old('equipement', $salle->equipement) }}</textarea>
                    @error('equipement')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', $salle->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">
                        Salle active
                    </label>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('salles.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection