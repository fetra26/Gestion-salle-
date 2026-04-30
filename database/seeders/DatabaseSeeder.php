<?php

namespace Database\Seeders;

use App\Models\Salle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les permissions
        Permission::firstOrCreate(['name' => 'view reservations']);
        Permission::firstOrCreate(['name' => 'create reservations']);
        Permission::firstOrCreate(['name' => 'validate reservations']);
        Permission::firstOrCreate(['name' => 'manage rooms']);
        Permission::firstOrCreate(['name' => 'manage users']);

        // Créer les rôles
        $roleUtilisateur = Role::firstOrCreate(['name' => 'utilisateur']);
        $roleUtilisateur->givePermissionTo(['view reservations', 'create reservations']);

        $roleResponsable = Role::firstOrCreate(['name' => 'responsable']);
        $roleResponsable->givePermissionTo(['view reservations', 'create reservations', 'validate reservations', 'manage rooms']);

        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->givePermissionTo(['view reservations', 'create reservations', 'validate reservations', 'manage rooms', 'manage users']);

        // Créer un utilisateur admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Créer un utilisateur responsable
        $responsable = User::firstOrCreate(
            ['email' => 'responsable@example.com'],
            [
                'name' => 'Responsable',
                'password' => Hash::make('password'),
            ]
        );
        $responsable->assignRole('responsable');

        // Créer un utilisateur lambda
        $utilisateur = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Utilisateur',
                'password' => Hash::make('password'),
            ]
        );
        $utilisateur->assignRole('utilisateur');

        // Créer des salles
        $salles = [
            ['nom' => 'Salle de réunion A', 'capacite' => 10, 'description' => 'Petite salle de réunion', 'equipement' => 'Tableau blanc, vidéo-projecteur'],
            ['nom' => 'Salle de réunion B', 'capacite' => 20, 'description' => 'Salle de réunion moyenne', 'equipement' => 'Tableau blanc, vidéo-projecteur, système de visioconférence'],
            ['nom' => 'Salle de conférence', 'capacite' => 50, 'description' => 'Grande salle de conférence', 'equipement' => 'Tableau blanc interactif, système audio, visioconférence'],
            ['nom' => 'Salle de formation', 'capacite' => 30, 'description' => 'Salle adaptée aux formations', 'equipement' => 'Ordinateurs, tableau blanc, vidéo-projecteur'],
        ];

        foreach ($salles as $salle) {
            Salle::firstOrCreate(['nom' => $salle['nom']], $salle);
        }
    }
}
