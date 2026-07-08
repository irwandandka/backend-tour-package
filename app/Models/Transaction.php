<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'status_id',
        'payment_method_id',
        'currency_id',
        'code',
        'quantity',
        'customer_name',
        'customer_email',
        'customer_phone',
        'address',
        'postal_code',
        'total_amount',
        'total_amount_base',
        'paid_amount',
        'booking_date',
        'notes',
        'date_from',
        'date_to',
        'expired_at',
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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function eticket()
    {
        return $this->hasOne(ETicket::class);
    }
}
