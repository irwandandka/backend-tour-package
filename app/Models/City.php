<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class City extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'name',
        'country_id',
        'image',
        'region_id',
        'postal_code',
        'latitude',
        'longitude'
    ];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'postal_code' => $this->postal_code,
        ];
    }

    public $incrementing = false;  // Disable auto-incrementing

    protected $keyType = 'string';  // Set the key type to string for UUID

    // Optionally, automatically generate a UUID for the primary key on creating a new record
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
