<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'building',
        'computers_count',
        'photos',
        'softwares',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'computers_count' => 'integer',
        'photos' => 'array',
        'softwares' => 'array',
    ];
}
