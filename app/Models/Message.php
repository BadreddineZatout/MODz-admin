<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'message', 'read'];

    public $timestamps = false;

    protected $casts = [
        'read' => 'boolean',
    ];
}
