<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Itinerary extends Model
{
    use SoftDeletes;

    protected $table = 'itineraries';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'language',
        'product_id',
        'day',
        'schedule_time',
        'title',
        'description',
        'caption',
        'latitude',
        'longitude',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
