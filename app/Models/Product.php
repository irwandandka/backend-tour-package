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
        'description',
        'thumbnail_image',
        'trip_length',
        'date_from',
        'date_until',
        'user_id',
        'city_id',
        'status_id',
        'category_id',
        'purchase_currency_id',
        'sales_currency_id',
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

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function product_prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function purchase_currency()
    {
        return $this->belongsTo(Currency::class, 'purchase_currency_id');
    }

    public function sales_currency()
    {
        return $this->belongsTo(Currency::class, 'sales_currency_id');
    }

    public function allotments()
    {
        return $this->hasMany(Allotment::class);
    }
}
