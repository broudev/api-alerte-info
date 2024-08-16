<?php

namespace App\Models\Quoideneufs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticlesModels extends Model
{
    use HasFactory;

    protected $fileable = [
        'rubrique_id',
        'genre_id',
        'pays_id',
        'titre',
        'lead',
        'author',
        'media_url',
        'contenus',
        'legende',
        'like_counter',
        'dislike_counter',
        'counter',
        'status',
        'slug',
    ];
}
