<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TransactionDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_detail_id',
        'discount_id',
        'user_id',
        'quantity',
        'quantity_adult',
        'quantity_child',
        'quantity_senior',
        'quantity_infant',
        'purchase_adult',
        'sales_adult',
        'purchase_child',
        'sales_child',
        'purchase_senior',
        'sales_senior',
        'purchase_infant',
        'sales_infant',
        'purchase_total',
        'purchase_total_base',
        'sales_total',
        'sales_total_base',
        'purchase_subtotal',
        'purchase_subtotal_base',
        'sales_subtotal',
        'sales_subtotal_base',
        'discount_amount',
        'discount_amount_base',
        'date_from',
        'date_to',
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

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productDetail()
    {
        return $this->belongsTo(ProductDetail::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
