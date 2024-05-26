<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Provider extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'shop_name',
        'phone_number',
        'phone_number2',
        'category_id',
        'province_id',
        'state_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
