<?php

namespace App\Models\AbonnementsMobileModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbonnementsMobileModels extends Model
{
    use HasFactory;
    protected $fileable = [
        'abonne_id',
        'abonne_forfait_id',
        'abonne_country_id',
        'montant_abonnements',
        'date_debut',
        'date_fin',
        'payments',
        'slug'
    ];
}
