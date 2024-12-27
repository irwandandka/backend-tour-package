<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory, Searchable, SoftDeletes;
    // use HasTranslations

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

    // public $translatable = ['name'];

    protected $fillable = [
        'name',
        'slug',
        'duration',
        'description',
        'price',
        'thumbnail_image',
        'capacity',
        'date_from',
        'date_until',
        'user_id',
        'city_id',
        'status_id',
        'category_id'
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function product_details()
    {
        return $this->hasMany(ProductDetail::class);
    }
}
