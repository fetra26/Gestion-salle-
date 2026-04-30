# Gestion des Salles — FMFP

Application web de gestion et réservation de salles de réunion, développée avec Laravel 12. Elle couvre le cycle complet d'une demande : soumission, validation/refus par un responsable, notifications par email, et visualisation dans un calendrier interactif.

---

## Fonctionnalités

### Réservations
- Soumission d'une demande depuis n'importe quelle page (modal sans redirection)
- Anti-double-réservation à 3 niveaux : vérification live JS, `lockForUpdate` en transaction, FormRequest
- Validation ou refus par un responsable avec motif obligatoire
- Refus automatique des demandes chevauchantes lors d'une confirmation
- Réservation directe (responsable/admin) — immédiatement confirmée
- Annulation avec motif obligatoire
- Modification et suppression par le demandeur (demandes en attente uniquement)

### Calendrier
- Vue semaine/mois/jour/liste style Outlook — FullCalendar 6
- Filtrage par salle et par statut
- Événements colorés par statut (vert = confirmée, orange = en attente, rouge = refusée, gris = annulée)
- Modal de détail au clic (durée, demandeur, description, lien fiche)

### Notifications email
- Email au demandeur à chaque changement de statut (soumise, confirmée, refusée, annulée)
- Envoi asynchrone via la file d'attente Laravel (queue)

### Administration
- Gestion des salles (CRUD) : nom, capacité, équipement, statut actif/inactif
- Gestion des utilisateurs (CRUD) : création avec assignation de rôle, modification, suppression
- Tableau de bord avec statistiques, graphiques (Chart.js), demandes à traiter

### UX / Interface
- Sidebar responsive avec menu hamburger sur mobile
- Toasts auto-dismiss pour les notifications de succès/erreur
- Formulaires en modal AJAX — pas de redirection de page
- Vérificateur de disponibilité en temps réel dans le formulaire
- Badges de statut en français
- Breadcrumbs sur toutes les pages de détail
- Empty states illustrés avec actions directes

---

## Stack technique

| Couche | Technologie |
|---|---|
| Framework | Laravel 12 (PHP 8.2+) |
| Authentification | Laravel Breeze |
| Rôles & permissions | spatie/laravel-permission 6 |
| Base de données | MySQL 8 |
| Frontend | Bootstrap 5.3 + Bootstrap Icons 1.11 |
| Bundler | Vite + Sass |
| Calendrier | FullCalendar 6.1 (CDN) |
| Graphiques | Chart.js 4.4 (CDN) |
| Emails | Laravel Mailables + Queue |
| Tests | PHPUnit 11 |

---

## Prérequis

- PHP 8.2 ou supérieur (avec les extensions `pdo_mysql`, `mbstring`, `openssl`)
- Composer
- Node.js 18+ et npm
- MySQL 8
- Un serveur SMTP (MailHog, Mailtrap, ou SMTP réel)

---

## Installation

### 1. Cloner le projet

```bash
git clone <url-du-depot> gestion-salles
cd gestion-salles
```

### 2. Installation automatique

```bash
composer run setup
```

Cette commande exécute dans l'ordre :
1. `composer install`
2. Copie de `.env.example` vers `.env`
3. `php artisan key:generate`
4. `php artisan migrate --force`
5. `npm install` + `npm run build`

### 3. Configurer l'environnement

Éditez `.env` :

```env
APP_NAME="Gestion Salles"
APP_URL=http://localhost:8000
APP_LOCALE=fr

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_salles
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@gestion-salles.fr"
MAIL_FROM_NAME="Gestion Salles"

QUEUE_CONNECTION=database
```

### 4. Peupler la base de données

```bash
php artisan migrate --seed
```

Le seeder crée :
- Les 3 rôles : `admin`, `responsable`, `utilisateur`
- Un compte admin par défaut (voir `DatabaseSeeder.php`)

### 5. Lancer l'application

```bash
composer run dev
```

Lance en parallèle :
- `php artisan serve` → http://localhost:8000
- `php artisan queue:listen` → traitement des emails
- `npm run dev` → Vite hot-reload

---

## Rôles et accès

| Fonctionnalité | Utilisateur | Responsable | Admin |
|---|:---:|:---:|:---:|
| Soumettre une demande | ✅ | ✅ | ✅ |
| Voir ses réservations | ✅ | ✅ | ✅ |
| Modifier/supprimer sa demande (en attente) | ✅ | ✅ | ✅ |
| Voir toutes les réservations | — | ✅ | ✅ |
| Valider / Refuser une demande | — | ✅ | ✅ |
| Réservation directe (confirmée immédiatement) | — | ✅ | ✅ |
| Annuler une réservation | — | ✅ | ✅ |
| Gérer les salles (CRUD) | — | ✅ | ✅ |
| Gérer les utilisateurs (CRUD) | — | — | ✅ |

---

## Structure du projet

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/UserController.php       # Gestion des utilisateurs (admin)
│   │   ├── DashboardController.php        # Dashboard + calendrier + API
│   │   ├── ReservationController.php      # CRUD réservations + actions
│   │   └── SalleController.php            # CRUD salles
│   └── Requests/
│       ├── StoreReservationRequest.php    # Validation création réservation
│       └── UpdateReservationRequest.php   # Validation modification
├── Mail/
│   ├── ReservationAnnulee.php
│   ├── ReservationConfirmee.php
│   ├── ReservationRefusee.php
│   └── ReservationSoumise.php
├── Models/
│   ├── Reservation.php
│   ├── Salle.php
│   └── User.php
├── Providers/
│   └── PermissionServiceProvider.php     # Directives Blade @role, @peutValider
└── Services/
    └── ReservationService.php             # Logique métier centralisée

resources/views/
├── admin/users/          # CRUD utilisateurs
├── components/
│   └── statut-badge.blade.php            # Composant badge statut FR
├── emails/               # Templates d'emails
├── layouts/
│   ├── app.blade.php                     # Layout principal + modals globaux
│   └── sidebar-nav.blade.php             # Navigation partagée
├── reservations/         # Vues réservations
└── salles/               # Vues salles

routes/
├── web.php               # Routes principales (statiques avant wildcards)
└── auth.php              # Routes Breeze (login, register, etc.)

tests/Unit/
└── ReservationServiceTest.php            # 11 cas de test
```

---

## Statuts d'une réservation

```
en_attente ──→ confirmee ──→ terminee
     │              │
     └──→ refusee   └──→ annulee
          annulee
```

| Statut | Couleur | Description |
|---|---|---|
| `en_attente` | Orange | Demande soumise, en attente de traitement |
| `confirmee` | Vert | Validée par un responsable |
| `refusee` | Rouge | Refusée (motif obligatoire) |
| `annulee` | Gris | Annulée (motif obligatoire) |
| `terminee` | Bleu clair | Créneau passé |

---

## API interne

| Méthode | Route | Description |
|---|---|---|
| `GET` | `/api/calendrier/reservations` | Événements FullCalendar (JSON) |
| `GET` | `/api/disponibilite` | Vérifie si un créneau est libre |

Paramètres de `/api/disponibilite` :
```
?salle_id=1&date_debut=2025-06-10T09:00&date_fin=2025-06-10T11:00
```
Réponse : `{ "disponible": true }` ou `{ "disponible": false }`

---

## Tests

```bash
composer run test
```

Les tests couvrent `ReservationService` :
- Création d'une demande
- Détection de conflit de créneau
- Validation avec refus automatique des demandes chevauchantes
- Refus avec motif obligatoire
- Annulation avec motif obligatoire
- Envoi des emails (Mail::fake)

---

## Commandes utiles

```bash
# Vider les caches après modification des routes
php artisan route:clear && php artisan cache:clear

# Recréer la base de données
php artisan migrate:fresh --seed

# Lancer les tests
php artisan test

# Voir les routes enregistrées
php artisan route:list
```

---

## Sécurité

- Protection CSRF sur tous les formulaires
- Middleware d'authentification sur toutes les routes
- Contrôle d'accès par rôle (spatie/laravel-permission)
- Verrouillage pessimiste (`lockForUpdate`) pour la détection de conflit
- Validation des entrées via FormRequest

---

## Licence

Usage interne FMFP. Tous droits réservés.
