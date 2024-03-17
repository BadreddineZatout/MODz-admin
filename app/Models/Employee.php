<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['first_name', 'last_name', 'phone', 'state_id', 'province_id', 'category_id', 'is_active', 'status'];
}
