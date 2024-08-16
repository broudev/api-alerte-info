<?php

namespace App\Models\AbonnementsMobileModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForfaitsAbonnementsMobileModels extends Model
{
    use HasFactory;

    protected $fileable = [
        'forfait_libelle',
        'montant_forfait',
        'duree_forfait',
        'slug'
    ];
}
