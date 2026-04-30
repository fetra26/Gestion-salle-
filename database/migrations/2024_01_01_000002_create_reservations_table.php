<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salle_id')->constrained()->onDelete('cascade');
            $table->foreignId('demandeur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->enum('statut', ['en_attente', 'confirmee', 'refusee', 'annulee', 'terminee'])->default('en_attente');
            $table->text('motif')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['salle_id', 'date_debut', 'date_fin']);
            $table->index(['demandeur_id', 'statut']);
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};