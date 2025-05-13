<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_id',
        'name_zh',
        'max_pax',
        'min_adult',
        'max_adult',
        'date_from',
        'date_until',
        'is_active',
        'is_featured',
        'activity_image',
        'product_id'
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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function product_prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function allotments()
    {
        return $this->hasMany(Allotment::class);
    }
}
