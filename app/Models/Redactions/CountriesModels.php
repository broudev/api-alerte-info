<?php

namespace App\Models\Redactions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountriesModels extends Model
{
    use HasFactory;

    protected $fileable = [
        'pays',
        'flag',
        'phone_code',
        'currency',
        'iso_code',
        'slug'
    ];
}
