<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueManager extends Model
{
    use HasFactory;
    protected $fileable = [
        'author',
        'content',
    ];
}
