<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobType extends Model
{
    protected $fillable = ['name', 'has_items'];

    public $timestamps = false;

    protected $casts = [
        'has_items' => 'boolean',
    ];
}
